<?php

use yii\db\Migration;

class m170927_171610_add_table_manufacturers extends Migration
{
    public function up()
    {
        if(Yii::$app->db->schema->getTableSchema('manufacturer') == null)
        {
            $this->createTable("manufacturer", array(
                "id"=>"int(11) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "gln"=>"bigint(13) DEFAULT NULL",
                "regNumber"=>"varchar(10) DEFAULT NULL",
                "name"=>"varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL",
                "nameRu"=>"varchar(255) DEFAULT NULL",
                "nameUk"=>"varchar(255) DEFAULT NULL",
                "nameEn"=>"varchar(255) DEFAULT NULL",
                "shortNameRu"=>"varchar(128) DEFAULT NULL",
                "shortNameUk"=>"varchar(128) DEFAULT NULL",
                "shortNameEn"=>"varchar(128) DEFAULT NULL",
                "imageLogo"=>"varchar(512) DEFAULT NULL",
                "description"=>"text DEFAULT NULL",
                "targetMarket"=>"varchar(32) DEFAULT NULL",
                "countPublishedProducts"=>"int(11) DEFAULT NULL",
                "contacts"=>"varchar(1000) DEFAULT NULL"
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }

    public function down()
    {
        $this->dropTable('manufacturer');
    }


}
