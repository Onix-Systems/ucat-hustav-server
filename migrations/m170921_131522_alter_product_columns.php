<?php

use yii\db\Schema;
use yii\db\Migration;

class m170921_131522_alter_product_columns extends Migration
{

    public function up()
    {
        $this->dropColumn('product', 'ManufacturerName');

        $this->addColumn('product', 'ManufacturerNameRu', 'varchar(128) DEFAULT NULL AFTER DescriptionTextEn');
        $this->addColumn('product', 'ManufacturerNameUk', 'varchar(128) DEFAULT NULL AFTER ManufacturerNameRu');
        $this->addColumn('product', 'ManufacturerNameEn', 'varchar(128) DEFAULT NULL AFTER ManufacturerNameUk');


        $this->alterColumn('product', 'CheeseFat', 'varchar(9) DEFAULT NULL');

        $this->addColumn('product', 'AdditionalTradeItemDescriptionRu', 'text DEFAULT NULL AFTER FunctionalNameTextEn');
        $this->addColumn('product', 'AdditionalTradeItemDescriptionUk', 'text DEFAULT NULL AFTER AdditionalTradeItemDescriptionRu');

        $this->alterColumn('product', 'CaloricValue', 'varchar(15) DEFAULT NULL');

        $this->alterColumn('product', 'Fats', 'varchar(15) DEFAULT NULL');
        $this->alterColumn('product', 'Carbs', 'varchar(15) DEFAULT NULL');
        $this->alterColumn('product', 'Proteins', 'varchar(15) DEFAULT NULL');
        $this->alterColumn('product', 'Sugar', 'varchar(15) DEFAULT NULL');
        $this->alterColumn('product', 'PercentageOfAlcohol', 'varchar(15) DEFAULT NULL');

        $this->alterColumn('product', 'StorageHandlingTemperatureMaximum', 'decimal(5,1) DEFAULT NULL');
        $this->alterColumn('product', 'StorageHandlingTemperatureMinimum', 'decimal(5,1) DEFAULT NULL');
    }

    public function down()
    {
        $this->addColumn('product', 'ManufacturerName', 'varchar(35) DEFAULT NULL');

        $this->dropColumn('product', 'ManufacturerNameUk');
        $this->dropColumn('product', 'ManufacturerNameEn');
        $this->dropColumn('product', 'ManufacturerNameRu');

        $this->alterColumn('product', 'CheeseFat', 'decimal(5,2) UNSIGNED DEFAULT NULL');

        $this->dropColumn('product', 'AdditionalTradeItemDescriptionRu');
        $this->dropColumn('product', 'AdditionalTradeItemDescriptionUk');

        $this->alterColumn('product', 'CaloricValue', 'decimal(7,2) UNSIGNED DEFAULT NULL');

        $this->alterColumn('product', 'Fats', 'char(12) DEFAULT NULL');
        $this->alterColumn('product', 'Carbs', 'char(12) DEFAULT NULL');
        $this->alterColumn('product', 'Proteins', 'char(12) DEFAULT NULL');
        $this->alterColumn('product', 'Sugar', 'char(12) DEFAULT NULL');
        $this->alterColumn('product', 'PercentageOfAlcohol', 'decimal(5,2) UNSIGNED DEFAULT NULL');

        $this->alterColumn('product', 'StorageHandlingTemperatureMaximum', 'decimal(3,1) DEFAULT NULL');
        $this->alterColumn('product', 'StorageHandlingTemperatureMinimum', 'decimal(3,1) DEFAULT NULL');

    }


}
