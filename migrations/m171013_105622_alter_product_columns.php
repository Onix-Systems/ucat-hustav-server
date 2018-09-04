<?php

use yii\db\Schema;
use yii\db\Migration;

class m171013_105622_alter_product_columns extends Migration
{

    public function up()
    {
        $this->addColumn('product', 'InformationProviderGLN', 'bigint(13) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('product', 'InformationProviderGLN');
    }

}
