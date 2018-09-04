<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;


use app\models\User;
use stdClass;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\rest\ActiveController;

class MainController extends ActiveController
{

    /**
     * @var array
     */
    private $errors;

    /**
     * @var User
     */
    private $user;

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }


    /**
     * @param array $data
     * @return string
     */
    private function renderJson(array $data) {
        header('Content-type: application/json');
        return Json::encode($data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function response(array $data = []) {
        $errors = $this->getErrors();
        if (!empty($errors) || isset($data['error'])) {
            return $this->renderError($data);
        } else {
            return $this->renderSuccess($data);
        }
    }

    /**
     * @return string
     */
    public function renderError($error = 404) {
        if (isset($error['error'])){
            $data = [
                'status' => $error['code'],
                'message' => $error['parameter'],
                'error' => $error['error']
            ];
        }
        else {
            $data = [
                'status' => $error,
                'message' => 'error',
                'data' => ['errors' => $this->getErrors()]
            ];
        }
        return $data;
    }

    /**
     * @param array $data
     * @return string
     */
    public function renderSuccess(array $data = []) {
        $result = [
            'status' => Yii::$app->response->statusCode,
            'message' => Yii::$app->response->statusText,
        ];
        if (isset($data['totalPages']) && $data['totalPages'] > 0){
            $result['totalPages'] = $data['totalPages'];
            unset($data['totalPages']);
        }
        $result['data'] = $data;
        return $result;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param string $parameter
     * @param string $message
     */
    public function addError($parameter, $message) {
        $this->errors[] = [
            'parameter' => $parameter,
            'message' => $message
        ];
    }

    /**
     * @param Model $model
     */
    public function loadErrorsFromModel(Model $model) {
        foreach ($model->getErrors() as $attribute => $messages) {
            foreach ($messages as $message) {
                $this->errors[] = [
                    'parameter' => $attribute,
                    'message' => $message
                ];
            }
        }
    }


}
