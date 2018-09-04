<?php

use yii\db\Migration;

class m171025_121214_add_table_cities extends Migration
{
    public function up()
    {
        if(Yii::$app->db->schema->getTableSchema('cities') == null)
        {
            $this->createTable("cities", array(
                "id"=>"int(11) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "Alfa2Code"=>"varchar(255) DEFAULT NULL",
                "nameEn"=>"varchar(255) DEFAULT NULL",
                "nameUk"=>"varchar(255) DEFAULT NULL",
                "nameRu"=>"varchar(255) DEFAULT NULL",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }

    public function down()
    {
        $this->dropTable('cities');
    }


}
