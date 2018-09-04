<?php

use yii\db\Schema;
use yii\db\Migration;

class m171020_103110_alter_user_columns extends Migration
{

    public function up()
    {
        $this->addColumn('user', 'userSmallImage', 'blob DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('user', 'userSmallImage');
    }

}
