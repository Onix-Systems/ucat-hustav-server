<?php

namespace app\api\v1\controllers;


use Yii;
use app\components\MainController;
use yii\filters\VerbFilter;
use vladimixz\BearerAuth;
use app\models\Product;

/**
 * Users controller for the `api/v1` module
 */
class RatingsController extends MainController
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
                ],
            ],
            'authenticator' => [
                'class' => BearerAuth::className(),
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
    public function actionView() {
        $post= Yii::$app->request->post();
        $gtins = json_decode($post['gtins']);
        $model = new Product();
        $data = [$model->ratings_list($gtins)];
        return $this->response($data);
    }

}
