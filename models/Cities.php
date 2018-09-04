<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property integer $id
 * @property string $Alfa2Code
 * @property string $nameEn
 * @property string $nameUk
 * @property string $nameRu
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private $uk = array(
        "'"=>"",
        "`"=>"",
        "а"=>"a","А"=>"A",
        "б"=>"b","Б"=>"B",
        "в"=>"v","В"=>"V",
        "г"=>"g","Г"=>"G",
        "д"=>"d","Д"=>"D",
        "е"=>"e","Е"=>"E",
        "ж"=>"zh","Ж"=>"Zh",
        "з"=>"z","З"=>"Z",
        "и"=>"i","И"=>"I",
        "й"=>"y","Й"=>"Y",
        "к"=>"k","К"=>"K",
        "л"=>"l","Л"=>"L",
        "м"=>"m","М"=>"M",
        "н"=>"n","Н"=>"N",
        "о"=>"o","О"=>"O",
        "п"=>"p","П"=>"P",
        "р"=>"r","Р"=>"R",
        "с"=>"s","С"=>"S",
        "т"=>"t","Т"=>"T",
        "у"=>"u","У"=>"U",
        "ф"=>"f","Ф"=>"F",
        "х"=>"h","Х"=>"H",
        "ц"=>"c","Ц"=>"C",
        "ч"=>"ch","Ч"=>"Ch",
        "ш"=>"sh","Ш"=>"Sh",
        "щ"=>"sch","Щ"=>"Sch",
        "ъ"=>"","Ъ"=>"",
        "ы"=>"y","Ы"=>"Y",
        "ь"=>"","Ь"=>"",
        "э"=>"e","Э"=>"E",
        "ю"=>"yu","Ю"=>"Yu",
        "я"=>"ya","Я"=>"Ya",
        "і"=>"i","І"=>"I",
        "ї"=>"yi","Ї"=>"Yi",
        "є"=>"e","Є"=>"E"
    );

    private $ru = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );

    public static function tableName()
    {
        return 'cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Alfa2Code', 'nameEn', 'nameUk', 'nameRu'], 'string', 'max' => 255],
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
        ];
    }

    public function GetInTranslit($string, $field_name) {
        $name = array(
            "nameEn" => '',
            "nameUk" => '',
            "nameRu" => '',
        );
        switch ($field_name) {
            case "nameEn":
                $name['nameEn'] = $string;
                $name['nameUk'] = iconv("UTF-8","UTF-8//IGNORE",strtr($string, array_flip($this->uk)));
                $name['nameRu'] = iconv("UTF-8","UTF-8//IGNORE",strtr($string, array_flip($this->ru)));
                break;
            case "nameRu":
                $name['nameEn'] = iconv("UTF-8","UTF-8//IGNORE",strtr($string, $this->ru));
                $name['nameUk'] = iconv("UTF-8","UTF-8//IGNORE",strtr($name['nameEn'], array_flip($this->uk)));
                $name['nameRu'] = $string;
                break;
            case "nameUk":
                $name['nameEn'] = iconv("UTF-8","UTF-8//IGNORE",strtr($string, $this->uk));
                $name['nameUk'] = $string;
                $name['nameRu'] = iconv("UTF-8","UTF-8//IGNORE",strtr($name['nameEn'], array_flip($this->ru)));
                break;
        }
        return $name;
    }


    public static function getCityId($name = false, $field_name, $country)
    {
        if(!$name) {
            $city_id = null;
        } else {
            $city = self::find()->select('id')->where('nameEn LIKE :name OR nameRu LIKE :name OR nameUk LIKE :name ', [':name' => $name])->one();
            if(!$city) {
                $city = new Cities();
            }
            $city->$field_name = $name;
            if($country) {
                $city->Alfa2Code = $country;
            }
            $city->save();
            $city_id = $city->id;
        }
        return $city_id;
    }

    public static function getCityName($id, $field_name)
    {
        if($id) {
            $city = self::find()->select($field_name)->where('id = :id ', [':id' => $id])->one();
            $city_name = (!empty($city->$field_name))?$city->$field_name :'';
        } else{
            $city_name = '';
        }
        return $city_name;
    }
}
