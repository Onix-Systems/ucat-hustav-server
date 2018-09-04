<?php

use yii\db\Schema;
use yii\db\Migration;

class m140717_100043_tables extends Migration
{
    public function up()
    {
        if(Yii::$app->db->schema->getTableSchema('user') == null)
        {
            $this->createTable("user", array(
                "id"=>"int(11) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "fullName"=>"varchar(255) DEFAULT NULL",
                "email"=>"varchar(255) DEFAULT NULL",
                "password"=>"varchar(255) DEFAULT NULL",
                "facebookId"=>"varchar(32) DEFAULT NULL",
                "twitterId"=>"varchar(32) DEFAULT NULL",
                "token"=>"varchar(255) DEFAULT NULL",
                "created"=>"varchar(30) DEFAULT NULL",
                "updated"=>"varchar(30) DEFAULT NULL",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        if(Yii::$app->db->schema->getTableSchema('user_history') == null)
        {
            $this->createTable("user_history", array(
                "id"=>"mediumint(8) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "user_id"=>"mediumint(8) NOT NULL",
                "gtin"=>"varchar(14) DEFAULT NULL",
                "action"=>"varchar(14) DEFAULT NULL",
                "rating"=>"int(2) DEFAULT NULL",
                "created"=>"datetime DEFAULT NULL",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        if(Yii::$app->db->schema->getTableSchema('product') == null)
        {
            $this->createTable("product", array(
                "id"=>"mediumint(8) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "recognize_gtin"=>"varchar(14) NOT NULL",
                "average_rating"=>"varchar(6) DEFAULT NULL",
                "nameUk"=>"varchar(50) DEFAULT NULL",
                "nameEn"=>"varchar(50) DEFAULT NULL",
                "nameRu"=>"varchar(50) DEFAULT NULL",
                "DescriptionTextRu" => "varchar(1000) DEFAULT NULL",
                "DescriptionTextUk" => "varchar(1000) DEFAULT NULL",
                "DescriptionTextEn" => "varchar(1000) DEFAULT NULL",
                "ManufacturerName" => "varchar(35) DEFAULT NULL",
                "CountryOfOrigin" => "smallint(4) unsigned DEFAULT NULL",
                "FunctionalNameTextRu" => "varchar(35) DEFAULT NULL",
                "FunctionalNameTextUk" => "varchar(35) DEFAULT NULL",
                "FunctionalNameTextEn" => "varchar(35) DEFAULT NULL",
                "AdditionalTradeItemDescriptionEn" => "text",
                "UkrZed" => "varchar(13) DEFAULT NULL",
                "CheeseFat" => "decimal(5,2) unsigned DEFAULT NULL",
                "GeneticallyModified" => "varchar(50) DEFAULT NULL",
                "CaloricValue" => "decimal(7,2) unsigned DEFAULT NULL",
                "Fats" => "char(12) DEFAULT NULL",
                "Carbs" => "char(12) DEFAULT NULL",
                "Proteins" => "char(12)  DEFAULT NULL",
                "PercentageOfAlcohol" => "decimal(5,2) unsigned DEFAULT NULL",
                "Sugar" => "char(12) DEFAULT NULL",
                "MinimumLifespanFromArrival" => "smallint(4) unsigned DEFAULT NULL",
                "MinimumLifespanFromProduction" => "smallint(4) unsigned DEFAULT NULL",
                "StorageHandlingTemperatureMaximum" => "decimal(3,1) DEFAULT NULL",
                "StorageHandlingTemperatureMinimum" => "decimal(3,1) DEFAULT NULL",
                "BrandName" => "varchar(50) DEFAULT NULL",
                "InformationProviderLogo" => "varchar(1000) NOT NULL",
                "created"=>"datetime DEFAULT NULL",
                "updated"=>"timestamp NULL DEFAULT CURRENT_TIMESTAMP",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        if(Yii::$app->db->schema->getTableSchema('product_image') == null)
        {
            $this->createTable("product_image", array(
                "id" => "int(11) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "product_id" => "mediumint(4) unsigned NOT NULL",
                "product_gtin"=>"varchar(14) NOT NULL",
                "name" => "varchar(255) NOT NULL",
                "link" => "varchar(1000) NOT NULL",
                "isPlanogram" => "tinyint(1) DEFAULT '0'",
                "created" => "datetime DEFAULT NULL",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
        
        if(Yii::$app->db->schema->getTableSchema('fogot_passwords') == null)
        {
            $this->createTable("fogot_passwords", array(
                "id"=>"mediumint(8) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "email"=>"varchar(50) NOT NULL",
                "key"=>"varchar(255) DEFAULT NULL",
                "create"=>"timestamp NULL DEFAULT CURRENT_TIMESTAMP",
            ), "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
        
    }

    public function down()
    {
        $this->dropTable('user');
        $this->dropTable('user_history');
        $this->dropTable('product');
        $this->dropTable('product_image');
        $this->dropTable('fogot_passwords');
    }
}
