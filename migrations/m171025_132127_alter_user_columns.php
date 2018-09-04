<?php

use yii\db\Schema;
use yii\db\Migration;

class m171025_132127_alter_user_columns extends Migration
{

    public function up()
    {
        $this->addColumn('user', 'Alfa2Code', 'varchar(255) DEFAULT NULL');
        $this->addColumn('user', 'city', 'int(11) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('user', 'Alfa2Code');
        $this->dropColumn('user', 'city');
    }

}
