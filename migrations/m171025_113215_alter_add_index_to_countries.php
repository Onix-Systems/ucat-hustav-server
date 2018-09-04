<?php

use yii\db\Schema;
use yii\db\Migration;

class m171025_113215_alter_add_index_to_countries extends Migration
{

    public function up()
    {
        $this->createIndex('Alfa2Code', 'countries', 'Alfa2Code', true );
    }

    public function down()
    {
        $this->dropIndex('Alfa2Code', 'countries');
    }
}
