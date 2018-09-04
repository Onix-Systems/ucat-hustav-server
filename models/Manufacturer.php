<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manufacturer".
 *
 * @property integer $id
 * @property integer $gln
 * @property string $regNumber
 * @property string $name
 * @property string $nameRu
 * @property string $nameUk
 * @property string $nameEn
 * @property string $shortNameRu
 * @property string $shortNameUk
 * @property string $shortNameEn
 * @property string $imageLogo
 * @property string $description
 * @property string $targetMarket
 * @property integer $countPublishedProducts
 * @property string $contacts
 */
class Manufacturer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manufacturer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gln', 'countPublishedProducts'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['regNumber'], 'string', 'max' => 10],
            [['name', 'shortNameRu', 'shortNameUk', 'shortNameEn'], 'string', 'max' => 128],
            [['nameRu', 'nameUk', 'nameEn'], 'string', 'max' => 255],
            [['imageLogo'], 'string', 'max' => 512],
            [['targetMarket'], 'string', 'max' => 32],
            [['contacts'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gln' => 'Gln',
            'regNumber' => 'Reg Number',
            'name' => 'Name',
            'nameRu' => 'Name Ru',
            'nameUk' => 'Name Uk',
            'nameEn' => 'Name En',
            'shortNameRu' => 'Short Name Ru',
            'shortNameUk' => 'Short Name Uk',
            'shortNameEn' => 'Short Name En',
            'imageLogo' => 'Image Logo',
            'description' => 'Description',
            'targetMarket' => 'Target Market',
            'countPublishedProducts' => 'Count Published Products',
            'contacts' => 'Contacts',
        ];
    }

    public function getRussianAlphabet() {
        $russianAlphabet = array();
        foreach (range(chr(0xC0), chr(0xDF)) as $a) {
            $russianAlphabet[] = iconv('CP1251', 'UTF-8', $a);
        }
        return $russianAlphabet;
    }

    public function get_all_manufacturers($params) {
        $items = Yii::$app->params['pages'];
        $offset = isset($params['page'])?$params['page']*$items-$items:0;
        $q = isset($params['q'])?$params['q']:'';
        $query = "SELECT `gln`, `name`, `imageLogo`, `targetMarket`, IF(name REGEXP '^[а-яА-Я]', 0, IF(name REGEXP '^[a-zA-Z]', 1, 2)) as sort, countries.nameUk as countries_nameUk FROM manufacturer LEFT JOIN countries ON countries.Alfa2Code = manufacturer.targetMarket WHERE `name` LIKE :q ORDER BY sort, name LIMIT :limit OFFSET :offset";
        $manufacturers = self::findBySql($query, [':limit' => $items, ':offset' => $offset, ':q' => $q . "%"])->asArray()->all();
        foreach ($manufacturers as $_manufacturers) {
            if(preg_match("/^[a-zа-яA-ZА-ЯёЁєЄіІїЇҐґ]/u", $_manufacturers['name'])) {
                $key = mb_strtoupper(mb_substr($_manufacturers['name'],0,1,"UTF-8"));
            } else
            {
                $key = 'other';
            }
            $manufacturersList[$key][] = $_manufacturers;
        }
        $query = "SELECT * FROM manufacturer WHERE `name` LIKE :q";
        $manufacturers_count = self::findBySql($query, [':q' => $q . "%"])->count("*");
        $manufacturersList['totalPages'] = (int)(($manufacturers_count + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
        return $manufacturersList;
    }

    public static function get_manufacturer_by_gln($post){
        $query = "SELECT *, countries.nameUk as countries_nameUk FROM manufacturer LEFT JOIN countries ON countries.Alfa2Code = manufacturer.targetMarket WHERE gln = :gln";
        $manufacturer = self::findBySql($query, [':gln' => $post['gln']])->asArray()->one();
        if(isset($manufacturer['contacts'])){
            $manufacturer['contacts'] = unserialize($manufacturer['contacts']);
        }
        $manufacturer = (!empty($manufacturer)) ? $manufacturer : null;
        return $manufacturer;
    }
    
}
