<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_image".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $product_gtin
 * @property string $name
 * @property string $link
 * @property integer $isPlanogram
 * @property string $created
 */
class ProductImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'product_gtin', 'name', 'link'], 'required'],
            [['product_id', 'isPlanogram'], 'integer'],
            [['created'], 'safe'],
            [['product_gtin'], 'string', 'max' => 14],
            [['name'], 'string', 'max' => 255],
            [['link'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'product_gtin' => 'Product Gtin',
            'name' => 'Name',
            'link' => 'Link',
            'isPlanogram' => 'Is Planogram',
            'created' => 'Created',
        ];
    }

    public function save_images($images_data, $result = true){
        if(isset($images_data['images']) && is_array(($images_data['images']))){
            foreach ($images_data['images'] as $image){
                if(strpos($image->link, '?') !== FALSE){
                    $image->link = substr($image->link, 0, strpos($image->link, '?'));
                }
                $model = new ProductImage();
                $model->setAttributes((array)$image);
                $model->setAttribute('product_id', $images_data['product_id']);
                $model->setAttribute('product_gtin', $images_data['product_gtin']);
                $model->setAttribute('created', date('Y:m:d H-i-s'));
                if(!$model->save()) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    public function find_images($product_id){
        $images = $this->find()->select(['name', 'link', 'isPlanogram'])->where('product_id = :product_id', [':product_id' => $product_id])->asArray()->all();
        return $images;
    }

    public function find_images_by_gtin($gtin){
        $images = $this->find()->select(['name', 'link', 'isPlanogram'])->where('product_gtin = :product_gtin', [':product_gtin' => $gtin])->asArray()->all();
        return $images;
    }

    public function find_image_by_gtin($gtin){
        $images = $this->find()->select(['name', 'link', 'isPlanogram'])->where('product_gtin = :product_gtin', [':product_gtin' => $gtin])->asArray()->one();
        return [$images];
    }
}
