<?php

use yii\db\Schema;
use yii\db\Migration;

class m171017_134555_alter_manufacturer_columns extends Migration
{

    public function up()
    {
        $this->addColumn('manufacturer', 'address', 'varchar(1000) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('manufacturer', 'address');
    }

}
