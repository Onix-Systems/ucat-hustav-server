<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use yii\base\Behavior;
use yii\helpers\Json;
use yii\base\Model;
use app\models\User;
use yii\web\Controller;

class ActionBehavior extends Behavior
{

    public $actions = [];

    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }


    public function beforeAction()
    {
        
    }

    const RULE_ACCEPTABLE = 1;
    const RULE_REQUIRED = 2;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $parameters;

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
     * @return string|null
     */
    public function getSessionKey() {
        if (isset($this->getParameters()['session_key'])) {
            return $this->getParameters()['session_key'];
        }
        return null;
    }

    /**
     * Constructor.
     * @param string $id the ID of this action
     * @param \yii\base\Controller $controller the controller that owns this action
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct()
    {
        $this->parameters = $_GET + $_POST;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
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
        if (empty($errors)) {
            return $this->renderSuccess($data);
        } else {
           return $this->renderError();
        }
    }

    /**
     * @return string
     */
    public function renderError($code = 404) {
        $data = [
            'status' => 'error',
            'code' => $code,
            'data' => ['errors' => $this->getErrors()]
        ];

        return $this->renderJson($data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function renderSuccess(array $data = []) {
        if (empty($data)) {
            $data = new \stdClass();
        }
        $data = [
            'status' => 'success',
            'data' => $data
        ];
        return $this->renderJson($data);
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

    /**
     * @return array
     */
    public function getRules() {
        return [];
    }

    /**
     * @return string
     */
    public function run() {
        $this->validation($this->getRules());
        if (!$this->getErrors()) {
            if ($this->getSessionKey()) {
                if ($user = $this->findUser()) {
                    $this->setUser($user);
                    return $this->runAfterValidation();
                }
                $this->addError('session_key', 'Session key is not a valid.');
                return $this->renderError();
            }
            return $this->runAfterValidation();
        }
        return $this->renderError();
    }

    /**
     * @return User
     */
    private function findUser() {
        return User::findOne(['session_key' => $this->getSessionKey()]);
    }

    /**
     * @param array $rules
     */
    public function validation(array $rules) {
        foreach ($rules as $ruleId => $ruleBody) {
            switch ($ruleId) {
                case self::RULE_ACCEPTABLE:
                    foreach ($this->getParameters() as $parameter => $value) {
                        $isValid = false;
                        foreach ($ruleBody['parameters'] as $parameter_) {
                            if ($parameter_ === $parameter) {
                                $isValid = true;
                                break;
                            }
                        }
                        if (!$isValid) {
                            $this->addError($parameter, 'Parameter "' . $parameter . '" is unknown.');
                        }
                    }
                    break;
                case self::RULE_REQUIRED:
                    foreach ($ruleBody['parameters'] as $parameter) {
                        if (!isset($this->getParameters()[$parameter])) {
                            $this->addError($parameter, 'Parameter "' . $parameter . '" is required.');
                        }
                    }
                    break;
            }
            if (isset($ruleBody['rules'])) {
                $this->validation($ruleBody['rules']);
            }
        }
    }


}
