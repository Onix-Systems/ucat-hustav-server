<?php

namespace app\api;

/**
 * api module definition class
 */

use Yii;
use yii\base\Module;

class api extends Module
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'json';
        $this->layout = false;
        $this->modules = [
            'v1' => [
                'class' => 'app\api\v1\v1',
            ],
        ];
    }
}
