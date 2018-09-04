<?php

namespace app\api\v1;

use yii\base\Module;

/**
 * v1 module definition class
 */
class v1 extends Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\api\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
