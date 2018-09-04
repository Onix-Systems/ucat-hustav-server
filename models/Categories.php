<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property integer $codeValue
 * @property string $nameEn
 * @property string $nameUk
 * @property string $nameRu
 * @property integer $typeOfHierarchy
 * @property string $createdAt
 * @property integer $countProducts
 */
class Categories extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codeValue', 'typeOfHierarchy'], 'required'],
            [['codeValue', 'typeOfHierarchy', 'countProducts'], 'integer'],
            [['createdAt'], 'safe'],
            [['nameEn', 'nameUk', 'nameRu'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'codeValue' => Yii::t('app', 'Code Value'),
            'nameEn' => Yii::t('app', 'Name En'),
            'nameUk' => Yii::t('app', 'Name Uk'),
            'nameRu' => Yii::t('app', 'Name Ru'),
            'typeOfHierarchy' => Yii::t('app', 'Type Of Hierarchy'),
            'createdAt' => Yii::t('app', 'Created At'),
            'countProducts' => Yii::t('app', 'Count Products'),
        ];
    }

    /**
     * @param $post
     * @return array
     *
     */
    public function get_categories($post){
        $items = Yii::$app->params['pages'];
        $offset = isset($post['page'])?$post['page']*$items-$items:0;
        $q = isset($post['q'])?$post['q']:'';
        $sorting_field = isset($post['sorting_field']) && in_array($post['sorting_field'], array_keys($this->attributeLabels())) ? $post['sorting_field']:'nameEn';
        $sorting_direction = isset($post['sorting_direction']) && $post['sorting_direction'] == 'desc' ? 'desc':'asc';
        $order = $sorting_field . ' ' . $sorting_direction;
        $categories = ArrayHelper::toArray(self::find()->where($sorting_field . ' LIKE :q', [':q' => $q . "%"])->andWhere('typeOfHierarchy = 4')->offset($offset)->limit($items)->orderBy($order)->all());
        $categoriesResult = (is_array($categories)) ? $categories : [];
        $categoriesCount = self::find()->where($sorting_field . ' LIKE :q', [':q' => $q . "%"])->andWhere('typeOfHierarchy = 4')->count();
        $categoriesResult['totalPages'] = (int)(($categoriesCount + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
        return $categoriesResult;
    }

    /**
     * @param $categoryId
     * @return mixed
     */
     public static function get_name($categoryId){
         $name = self::find()->select('nameRu')->where('codeValue = :codeValue', [':codeValue' => $categoryId])->asArray()->one();
         return $name['nameRu'];
    }

     public static function saveCountProducts(){
         $categories = self::find()->where('typeOfHierarchy = 4')->all();
         foreach ($categories as $category) {
             $countProducts = Product::find()->where(['Brick' => $category->codeValue])->count();
             if($category->countProducts != $countProducts) {
                 $category->countProducts = $countProducts;
                 $category->save();
             }
         }
    }
}
