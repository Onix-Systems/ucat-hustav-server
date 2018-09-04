<?php
namespace app\api\v1\controllers;

use Yii;
use app\components\MainController;
use yii\filters\VerbFilter;
use app\models\UserHistory;
use app\models\User;
use vladimixz\BearerAuth;
use app\models\Product;
use app\models\ProductImage;

/**
 * Users controller for the `api/v1` module
 */
class HistoryController extends MainController
{

    public $modelClass = 'app\models\UserHistory';

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    /**
     * @inheritdoc
     */
    public function behaviors () {
        $this->enableCsrfValidation = false;
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['get'],
                    'save' => ['post'],
                    'feed' => ['get'],
                ],
            ],
            'authenticator' => [
                'class' => BearerAuth::className(),
                'except' => ['feed']
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();
        unset($actions['view']);
        return $actions;
    }

    /**
     * @return array
     */
    public function actionSave() {
        $response = $this->saveHistory();
        return $this->response($response);
    }

    /**
     * @return array
     */
    public function actionView() {
        $response = $this->getHistory();
        return $this->response($response);
    }

    /**
     * @return array
     */
    public function actionFeed() {
        $params = Yii::$app->request->get();
        unset($params['user_id']);
        $params['items'] = (isset($params['items']))? $params['items'] : 25;
        $feed = $this->getLastRatings($params);
        $model = new UserHistory();
        $feed['totalPages'] = $model->last_ratings_count(false, $params['items']);
        return $this->response($feed);
    }

    /**
     * @return array
     */
    public function saveHistory(){
        $post = Yii::$app->request->post();
        $token = Yii::$app->request->getHeaders()->get('Authorization');
        if(!isset($post['gtin'])){
            return $data = ['error' => 'Invalid GTIN.', 'parameter' => 'gtin', 'code' => '400'];
        } else if (empty($token)){
            return $data = ['error' => 'Parameter token is required.', 'parameter' => 'token', 'code' => '400'];
        }
        $model = new UserHistory();
        $product_model = new Product();
        $model->setAttributes($post);
        $model->setAttribute('created', date('Y-m-d H:i:s'));
        $user_model = new User();
        // check token in database
        $user = $user_model->check_token($token);
        if ($user == 'Invalid token!'){
            return $data = ['error' => 'Invalid token', 'parameter' => 'token', 'code' => '400'];
        }
        $model->setAttribute('user_id', $user->id);
        //save user data
        if($model->validate()){
            if($post['action'] == 'scanned' && $product_model->gtin_verification($post['gtin'])=='In base!'){
                $data = [$model->verifyGtin($user->id, $post['gtin'])];
            } else if($post['action'] == 'scanned' && $product_model->gtin_verification($post['gtin'])!='In base!'){
                return $data = ['error' => 'Product is not found', 'parameter' => 'GTIN', 'code' => '400'];
            } else if($post['action'] == 'viewed'){
                $data = [$model->save_view($user->id, $post['gtin'])];
            } else if($post['action'] == 'voted'){
                if(empty($post['gtin'])) {
                    $data = ['error' => 'Parameter token is required for vote.', 'parameter' => 'action', 'code' => '404'];
                } else {
                    $data = [$model->save_rating($user->id, $post['gtin'])];
                    $product_model->save_average_rating($post['gtin']);
                }
            } else {
                return $data = ['error' => 'Wrong action!', 'parameter' => 'action', 'code' => '404'];
            }
        }
        if(!$data) {
            $data = ['error' => 'Wrong params!', 'parameter' => 'params', 'code' => '400'];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getHistory(){
        $params = Yii::$app->request->get();
        $token = Yii::$app->request->getHeaders()->get('Authorization');
        if(!isset($params['page'])){
            $params['page'] = 1;
        }
        $model = new UserHistory();
        $model->setAttributes($params);
        $model->setAttribute('created', date('Y-m-d H:i:s'));
        $user_model = $user = new User();
        // check token in database
        if (!empty($token)){
            $user = $user_model->check_token($token);
            if ($user == 'Invalid token!'){
                return $data = ['error' => 'Invalid token', 'parameter' => 'token', 'code' => '400'];
            }
            $model->setAttribute('user_id', $user->id);
        }
        $user_id = $user->id > 0 ? $user->id : 0;
        if($params['action'] == 'scan'){
            $data = $this->getAllScans($params, $user);
            $data['totalPages'] = $model->get_last_scan_count($user_id);
        } else if ($params['action'] == 'ratings'){
            $params['user_id'] = $user_id;
            $data = $this->getLastRatings($params);
            $totalPages = $model->last_ratings_count($user_id);
            if($totalPages) {
                $data['totalPages'] = $totalPages;
            }
        } else if($params['action'] == 'all') {
            $data = $this->getAllHistory($params, $user);
            $data['totalPages'] = $model->get_all_history_count($user_id);
        } else {
            return $data = ['error' => 'Wrong action!', 'parameter' => 'action', 'code' => '404'];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getAllHistory($params, $user){
        $model = new UserHistory();
        $images_model = new ProductImage();
        $product_model = new Product();
        $values = ['user_id' => $user->id, 'page' => $params['page']];
        $history = $model->get_all_history($values);
        if(!empty($history)){
            foreach ($history as $key => $row) {
                $history[$key]['product'] = $product_model->find_gtin($history[$key]['gtin']);
                $history[$key]['product']['images'] = $images_model->find_images_by_gtin($history[$key]['gtin']);
                unset($history[$key]['gtin']);
            }
        } else {
            return $data = ['error' => 'History is empty', 'parameter' => 'history', 'code' => '400'];
        }
        return $history;
    }

    /**
     * @return array
     */
    public function getLastRatings($params){
        $model = new UserHistory();
        $images_model = new ProductImage();
        $product_model = new Product();
        $values = ['user_id' => $params['user_id'], 'page' => $params['page'], 'items' => $params['items']];
        $history = $model->last_ratings($values);
        if(!empty($history)){
            foreach ($history as $key => $row) {
                $history[$key]['product'] = $product_model->find_gtin($history[$key]['gtin'], true);
                $history[$key]['product']['images'] = $images_model->find_image_by_gtin($history[$key]['gtin']);
                $history[$key]['product']['votes'] = $model->find_votes_by_id($history[$key]['id']);
                unset($history[$key]['gtin'], $history[$key]['id']);
            }
        } else {
            return ['message' => 'No ratings'];
        }
        return $history;
    }

    /**
     * @return array
     */
    public function getAllScans($params, $user){
        $model = new UserHistory();
        $images_model = new ProductImage();
        $product_model = new Product();
        $values = ['user_id' => $user->id, 'page' => $params['page']];
        $history = $model->last_scan($values);
        if(!empty($history)){
            foreach ($history as $key => $row) {
                $history[$key]['product'] = $product_model->find_gtin($history[$key]['gtin']);
                $history[$key]['product']['images'] = $images_model->find_images_by_gtin($history[$key]['gtin']);
                unset($history[$key]['gtin']);
            }
        } else {
            return $data = ['error' => 'No scans', 'parameter' => 'history', 'code' => '400'];
        }
        return $history;
    }
}
