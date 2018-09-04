<?php

use yii\db\Schema;
use yii\db\Migration;

class m171108_172102_alter_categories_columns extends Migration
{

    public function up()
    {
        $this->addColumn('categories', 'countProducts', 'int(11) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('categories', 'countProducts');
    }

}
