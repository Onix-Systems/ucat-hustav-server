<?php

use yii\db\Schema;
use yii\db\Migration;

class m171013_122021_alter_user_columns extends Migration
{

    public function up()
    {
        $this->addColumn('user', 'userImage', 'mediumblob DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('user', 'userImage');
    }

}
