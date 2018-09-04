<?php

use yii\db\Migration;

class m171002_181211_add_table_countries extends Migration
{
    public function up()
    {
        if(Yii::$app->db->schema->getTableSchema('countries') == null)
        {
            $this->createTable("countries", array(
                "id"=>"int(11) UNSIGNED NOT NULL",
                "Alfa2Code"=>"varchar(255) DEFAULT NULL",
                "nameEn"=>"varchar(255) DEFAULT NULL",
                "nameUk"=>"varchar(255) DEFAULT NULL",
                "nameRu"=>"varchar(255) DEFAULT NULL",
                "sort"=>"smallint(5) UNSIGNED NOT NULL DEFAULT '10'",
                "Alfa3Code"=>"varchar(255) DEFAULT NULL",
                "LangCode"=>"varchar(255) DEFAULT NULL",
                "codeValue"=>"char(3) DEFAULT NULL",
                "use4Subscription"=>"tinyint(1) NOT NULL DEFAULT '0'",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }

    public function down()
    {
        $this->dropTable('countries');
    }


}
