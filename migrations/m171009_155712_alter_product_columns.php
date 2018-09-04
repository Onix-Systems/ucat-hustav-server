<?php

use yii\db\Schema;
use yii\db\Migration;

class m171009_155712_alter_product_columns extends Migration
{

    public function up()
    {
        $this->addColumn('product', 'Brick', 'int(8) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('product', 'Brick');
    }

}
