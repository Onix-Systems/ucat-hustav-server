<?php

namespace app\api\v1\controllers;


use app\models\Categories;
use Yii;
use app\components\MainController;
use yii\filters\VerbFilter;
use vladimixz\BearerAuth;
use app\models\Product;

/**
 * Product controller for the `api/v1` module
 */
class ProductController extends MainController
{

    public $modelClass = 'app\models\Product';

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
                    'view' => ['post'],
                    'product-manufacturer' => ['post'],
                    'product-category' => ['post'],
                    'categories' => ['post'],
                    'save-all-product' => ['post'],
                    'update-gtins' => ['post'],
                    'create-product' => ['post'],
                ],
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
    public function actionView(){
        $post = Yii::$app->request->post();
        if(!isset($post['gtin']) || empty($post['gtin'])) {
            $product = ['error' => 'Invalid GTIN', 'parameter' => 'gtin', 'code' => '401'];
        } else {
            $model = new Product();
            $product = $model->gtin_verification($post['gtin'], true);
            $product = (is_array($product) && isset($product['recognize_gtin']))? $product:['error' => 'Invalid GTIN', 'parameter' => 'gtin', 'code' => '401'];
        }
        return $this->response($product);
    }
    
    /**
     * @return array
     */
    public function actionProductManufacturer() {
        $post = Yii::$app->request->post();
        if(!isset($post['gln'])){
            return $this->response(['error' => 'Parameter gln is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = new Product();
        $products = $model->get_product_manufacturer($post);
        if(empty($products)){
            return $this->response(['error' => 'Products not found.', 'parameter' => 'gln', 'code' => '200']);
        }
        return $this->response($products);
    }

    /**
     * @return array
     */
    public function actionProductCategory() {
        $post = Yii::$app->request->post();
        if(!isset($post['category'])){
            return $this->response(['error' => 'Parameter category is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = new Product();
        $products = $model->get_product_category($post);
        if(empty($products)){
            return ['message' => 'Products not found.'];
        }
        return $this->response($products);
    }

    /**
     * @return array
     */
    public function actionCategories() {
        $post = Yii::$app->request->post();
        $model = new Categories();
        $categories = $model->get_categories($post);
        if(empty($categories)){
            return $this->response(['error' => 'Categories not found.', 'parameter' => 'gln', 'code' => '200']);
        }
        return $this->response($categories);
    }
    
    /**
     * @return array
     */
    public function actionSaveAllProduct() {
        $post = Yii::$app->request->post();
        if(!isset($post['token'])){
            return $this->response(['error' => 'Parameter token is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = new Product();
        $model->save_all_product();
    }
    
    /**
     * @return array
     */
    public function actionUpdateGtins() {
        $post = Yii::$app->request->post();
        if(!isset($post['token'])){
            return $this->response(['error' => 'Parameter token is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = new Product();
        $model->last_updated_gtins();
    }

    /**
     * @return array
     */
    public function actionCreateProduct() {
        $token = Yii::$app->request->getHeaders()->get('Authorization');
        $post = Yii::$app->request->post();
        if(!isset($post['recognize_gtin']) || empty($post['recognize_gtin'])){
            return $this->response(['error' => 'Parameter recognize_gtin is required.', 'parameter' => 'recognize_gtin', 'code' => '200']);
        } else if (empty($token)){
            return $data = ['error' => 'For add products you have to be authorized', 'parameter' => 'token', 'code' => '400'];
        }
        $model = new Product();
        if($model->find_id($post['recognize_gtin'])) {
            $result =  $this->response(['error' => 'This product already exist', 'parameter' => 'recognize_gtin', 'code' => '200']);
        } else {
            $result = $model->create_product($post);
        }
        return $result;
    }
    

}
