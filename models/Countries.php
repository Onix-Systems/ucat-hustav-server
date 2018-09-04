<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property integer $id
 * @property string $Alfa2Code
 * @property string $nameEn
 * @property string $nameUk
 * @property string $nameRu
 * @property integer $sort
 * @property string $Alfa3Code
 * @property string $LangCode
 * @property string $codeValue
 * @property integer $use4Subscription
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'sort', 'use4Subscription'], 'integer'],
            [['Alfa2Code', 'nameEn', 'nameUk', 'nameRu', 'Alfa3Code', 'LangCode'], 'string', 'max' => 255],
            [['codeValue'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'Alfa2Code' => Yii::t('app', 'Alfa2 Code'),
            'nameEn' => Yii::t('app', 'Name En'),
            'nameUk' => Yii::t('app', 'Name Uk'),
            'nameRu' => Yii::t('app', 'Name Ru'),
            'sort' => Yii::t('app', 'Sort'),
            'Alfa3Code' => Yii::t('app', 'Alfa3 Code'),
            'LangCode' => Yii::t('app', 'Lang Code'),
            'codeValue' => Yii::t('app', 'Code Value'),
            'use4Subscription' => Yii::t('app', 'Use4 Subscription'),
        ];
    }
}
