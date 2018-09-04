<?php

use yii\db\Schema;
use yii\db\Migration;

class m141020_145219_provider_logo extends Migration
{
    public function up()
    {
        $this->alterColumn('product', 'InformationProviderLogo', 'mediumblob NULL');
    }

    public function down()
    {
        
    }
}
