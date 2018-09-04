<?php
namespace app\api\v1\controllers;

use app\models\Manufacturer;
use app\models\Product;
use Yii;
use app\components\MainController;
use yii\filters\VerbFilter;
use vladimixz\BearerAuth;
use yii\helpers\Json;

/**
 * Users controller for the `api/v1` module
 */
class ManufacturersController extends MainController
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
                    'list' => ['post'],
                    'save' => ['get'],
                ],
            ],
            'authenticator' => [
                'class' => BearerAuth::className(),
                'except' => ['manufacturers-list', 'manufacturer', 'update-manufacturer']
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();
        unset($actions['list']);
        return $actions;
    }

    /**
     * @return array
     */
    public function actionSave() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] . '/manufacturers?authKey=' . Yii::$app->params['authKey']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);
        foreach ($data as $_data)
        {
            if(is_null(Manufacturer::find_id($_data->primaryGLN))){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] .  '/manufacturer/' . $_data->primaryGLN . '/?authKey=' . Yii::$app->params['authKey']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                $output = curl_exec($ch);
                curl_close($ch);
                $productsTotal = Product::find()->select(['*'])->where('InformationProviderGLN = :InformationProviderGLN', [':InformationProviderGLN' => $_data->primaryGLN])->count();
                $manufacturer = json_decode($output);
                $contacts = serialize((array)$manufacturer->contacts);
                $model = new Manufacturer();
                $model->gln = $manufacturer->primaryGLN;
                $model->regNumber = $manufacturer->regNumber;
                $model->name = $manufacturer->name;
                $model->nameRu = $manufacturer->nameRu;
                $model->nameUk = $manufacturer->nameUk;
                $model->nameEn = $manufacturer->nameEn;
                $model->shortNameRu = $manufacturer->shortNameRu;
                $model->shortNameUk = $manufacturer->shortNameUk;
                $model->shortNameEn = $manufacturer->shortNameEn;
                $model->imageLogo = $manufacturer->logo;
                $model->description = $manufacturer->description;
                $model->targetMarket = $manufacturer->targetMarket;
                $model->countPublishedProducts = $productsTotal;
                $model->contacts = $contacts;
                $model->save();
                sleep(0.1);
            }
        }
    }

    /**
     * @return array
     */
    public function actionManufacturersList() {
        $post = Yii::$app->request->post();
        $model = new Manufacturer();
        return $this->response($model->get_all_manufacturers($post));
    }

    /**
     * @return array
     */
    public function actionManufacturer() {
        $post = Yii::$app->request->post();
        if(!isset($post['gln'])){
            return $this->response(['error' => 'Parameter gln is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = new Manufacturer();
        $manufacturer = $model->get_manufacturer_by_gln($post);
        if(empty($manufacturer)){
            return $this->response(['error' => 'Manufacturer is not found.', 'parameter' => 'gln', 'code' => '200']);
        }
        return $this->response($model->get_manufacturer_by_gln($post));
    }

    /**
     * @return array
     */
    public function actionUpdateManufacturer() {
        $post = Yii::$app->request->post();
        if(!isset($post['token'])){
            return $this->response(['error' => 'Parameter token is required.', 'parameter' => 'gln', 'code' => '200']);
        }
        $model = Manufacturer::find()->all();
        foreach ($model as $_model) {
            $productsTotal = Product::find()->select(['*'])->where('InformationProviderGLN = :InformationProviderGLN', [':InformationProviderGLN' => $_model->gln])->count();
            $_model->countPublishedProducts = $productsTotal;
            $_model->save();
        }
    }

}
