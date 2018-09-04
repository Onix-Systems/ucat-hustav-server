<?php

use yii\db\Migration;

class m171009_091010_add_table_categories extends Migration
{
    public function up()
    {
        if(Yii::$app->db->schema->getTableSchema('categories') == null)
        {
            $this->createTable("categories", array(
                "id"=>"int(11) UNSIGNED NOT NULL",
                "codeValue"=>"int(8) NOT NULL",
                "nameEn"=>"varchar(255) DEFAULT NULL",
                "nameUk"=>"varchar(255) DEFAULT NULL",
                "nameRu"=>"varchar(255) DEFAULT NULL",
                "typeOfHierarchy"=>"tinyint(1) NOT NULL",
                "createdAt"=>"datetime DEFAULT NULL",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }
    public function down()
    {
        $this->dropTable('categories');
    }


}
