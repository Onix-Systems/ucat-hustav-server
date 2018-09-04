<?php

namespace app\api\v1\controllers;

use app\models\Cities;
use app\models\Countries;
use app\models\FogotPasswords;
use Yii;
use app\components\MainController;
use yii\filters\VerbFilter;
use app\models\User;
use yii\web\UnauthorizedHttpException;
use app\models\UserDevice;
use vladimixz\BearerAuth;


/**
 * Users controller for the `api/v1` module
 */
class UserController extends MainController
{

    public $modelClass = 'app\models\User';

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
                    'register' => ['post'],
                    'login' => ['post'],
                    'logout' => ['post'],
                    'forgot-password' => ['post'],
                    //'profile' => ['post'],
                    'view' => ['get', 'post'],
                    'device' => ['post'],
                    'device-edit' => ['put'],
                    'device-delete' => ['delete'],
                    'countries' => ['get'],
                ],
            ],
            'authenticator' => [
                'registerAction' => 'register',
                'loginAction' => 'login',
                'class' => BearerAuth::className(),
                'except' => ['forgot-password', 'countries']
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions () {
        $actions = parent::actions();
        unset($actions['view']);
        return $actions;
    }

    /**
     * @return array
     */
    public function actionRegister () {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => ['token' => Yii::$app->user->identity->token]
        ];
    }

    /**
     * @return array
     * @throws UnauthorizedHttpException
     */
    public function actionLogin () {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => ['token' => Yii::$app->user->identity->token]
        ];
    }

    /**
     * @return array
     */
    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $user->setAttribute('token', null);
        $result = [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText
        ];
        if ($user->save()) {
            $result['data'] = ['success' => 'Successful logout'];
        } else {
            $result['data'] = ['error' => 'Logout error!'];
        }
        return $result;
    }

    /**
     * @return array
     */
/*    public function actionProfile() {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => []
        ];
    }*/

    /**
     * @return array
     */
    public function actionView()
    {
        $post = Yii::$app->request->post();
        if(empty($post['id'])) {
            $get = Yii::$app->request->get();
            $field_name = isset($get['field_name'])? $get['field_name']: "nameEn";
            $profile = Yii::$app->user->identity;
            unset($profile['password']);
            unset($profile['token']);
            unset($profile['fullName']);
            $profile['city'] = Cities::getCityName($profile['city'], $field_name);
            $result = [
                'status' => Yii::$app->response->statusCode,
                'message' => Yii::$app->response->statusText,
                'data' => [$profile]
            ];
        } else {
            if(Yii::$app->user->id != $post['id']) {
                $result = [
                    'status' => Yii::$app->response->statusCode,
                    'message' => Yii::$app->response->statusText,
                    'data' => ['error' => 'Wrong Id']
                ];
            } else {
                $user = User::findOne($post['id']);
                $field_name = isset($post['field_name'])? $post['field_name']: "nameEn";
                if(isset($post['city'])) {
                    $alfa2Code = isset($post['Alfa2Code'])? $post['Alfa2Code']: null;
                    $post['city'] = Cities::getCityId($post['city'], $field_name, $alfa2Code);
                }

                unset($post['token'], $post['id'], $post['token'], $post['facebookId'], $post['twitterId'], $post['created']);
                $post['update'] = date('c');

                if(!empty($post['password'])) {
                    $post['password'] = Yii::$app->getSecurity()->generatePasswordHash($post['password']);
                }

                $user->setAttributes($post);

                if ($user->save()){
                    $result = [
                        'status' => Yii::$app->response->statusCode,
                        'message' => Yii::$app->response->statusText,
                        'data' => ['success' => 'Your profile was updated']
                    ];
                }else {
                    $result = [
                        'status' => Yii::$app->response->statusCode,
                        'message' => Yii::$app->response->statusText,
                        'data' => ['error' => $user->getErrors()]
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function actionDevice()
    {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => []
        ];
    }

    /**
     * @return array
     */
    public function actionDeviceEdit()
    {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => []
        ];
    }

    /**
     * @return array
     */
    public function actionDeviceDelete()
    {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => []
        ];
    }

    /**
     * @return array
     */
    public function actionForgotPassword()
    {
        $post = Yii::$app->request->post();
        if(!empty($post['token'])) {
            $model = new User();
            $drop_key = new FogotPasswords();
            $empty_key = $drop_key->empty_key($post);
            if($empty_key == TRUE){
                return $this->response(array('error' => ['Wrong link'], 'code' => 200, 'parameter' => 'Wrong link'));
            }
            if ($model->newPass()) {
                $drop_key->drop_key($post);
                return $this->response(['New password confirm']);
            } else {
                return $this->response(array('error' => ['Error change password'], 'code' => 200, 'parameter' => 'Error change password'));
            }
        }

        $userModel = new User();
        $userModel->setAttributes($post);
        $data = ['We sent a link to reset your password to your registered Email address.'];
        // send email with forgot password link
        $key = $userModel->forgot_password($post);
        if($key) {
            $passwordsModel = new FogotPasswords();
            $passwordsModel->setAttributes($key);
            // save forgot password key
            $passwordsModel->saveKey($key);
        } else {
            $data = ['error' => 'Wrong email.', 'parameter' => 'email', 'code' => '200'];
        }

        return $this->response($data);
    }

    /**
     * @return array
     */
    public function actionForgotRedirect()
    {
        return [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
            'data' => []
        ];
    }

    /**
     * @return array
     */
    public function actionCountries()
    {
        $params = Yii::$app->request->get();
        $order_field = (!empty($params['order_field']) && in_array($params['order_field'], array('nameEn', 'nameUk', 'nameRu'))) ? $params['order_field'] : 'nameEn';
        $countries = Countries::find()->select(['Alfa2Code', $order_field . ' name'])->orderBy('name')->asArray()->all();
        if(!$countries) {
            return $this->response(array('error' => ['Error request']));
        }
        return $this->response($countries);
    }
}
