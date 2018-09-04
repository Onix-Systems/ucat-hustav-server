<?php

use yii\db\Schema;
use yii\db\Migration;

class m170921_115517_alter_product_columns extends Migration
{
    public function up()
    {
        $this->alterColumn('product', 'FunctionalNameTextRu', 'varchar(255) DEFAULT NULL');
        $this->alterColumn('product', 'FunctionalNameTextUk', 'varchar(255) DEFAULT NULL');
        $this->alterColumn('product', 'FunctionalNameTextEn', 'varchar(255) DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('product', 'FunctionalNameTextRu', 'varchar(35) DEFAULT NULL');
        $this->alterColumn('product', 'FunctionalNameTextUk', 'varchar(35) DEFAULT NULL');
        $this->alterColumn('product', 'FunctionalNameTextEn', 'varchar(35) DEFAULT NULL');
    }
}
