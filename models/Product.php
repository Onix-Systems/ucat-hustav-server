<?php

namespace app\models;

use stdClass;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $recognize_gtin
 * @property string $average_rating
 * @property string $nameUk
 * @property string $nameEn
 * @property string $nameRu
 * @property string $DescriptionTextRu
 * @property string $DescriptionTextUk
 * @property string $DescriptionTextEn
 * @property string $ManufacturerNameRu
 * @property string $ManufacturerNameUk
 * @property string $ManufacturerNameEn
 * @property integer $CountryOfOrigin
 * @property string $FunctionalNameTextRu
 * @property string $FunctionalNameTextUk
 * @property string $FunctionalNameTextEn
 * @property string $AdditionalTradeItemDescriptionRu
 * @property string $AdditionalTradeItemDescriptionUk
 * @property string $AdditionalTradeItemDescriptionEn
 * @property string $UkrZed
 * @property string $CheeseFat
 * @property string $GeneticallyModified
 * @property string $CaloricValue
 * @property string $Fats
 * @property string $Carbs
 * @property string $Proteins
 * @property string $PercentageOfAlcohol
 * @property string $Sugar
 * @property integer $MinimumLifespanFromArrival
 * @property integer $MinimumLifespanFromProduction
 * @property string $StorageHandlingTemperatureMaximum
 * @property string $StorageHandlingTemperatureMinimum
 * @property string $BrandName
 * @property string $InformationProviderLogo
 * @property string $created
 * @property string $updated
 * @property string $Brick
 * @property integer $InformationProviderGLN
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recognize_gtin'], 'required'],
            [['CountryOfOrigin', 'MinimumLifespanFromArrival', 'MinimumLifespanFromProduction', 'Brick', 'InformationProviderGLN'], 'integer'],
            [['AdditionalTradeItemDescriptionRu', 'AdditionalTradeItemDescriptionUk', 'AdditionalTradeItemDescriptionEn', 'InformationProviderLogo'], 'string'],
            [['StorageHandlingTemperatureMaximum', 'StorageHandlingTemperatureMinimum'], 'number'],
            [['created', 'updated'], 'safe'],
            [['recognize_gtin'], 'string', 'max' => 14],
            [['average_rating'], 'string', 'max' => 6],
            [['nameUk', 'nameEn', 'nameRu', 'GeneticallyModified', 'BrandName'], 'string', 'max' => 50],
            [['DescriptionTextRu', 'DescriptionTextUk', 'DescriptionTextEn'], 'string', 'max' => 1000],
            [['ManufacturerNameRu', 'ManufacturerNameUk', 'ManufacturerNameEn'], 'string', 'max' => 128],
            [['FunctionalNameTextRu', 'FunctionalNameTextUk', 'FunctionalNameTextEn'], 'string', 'max' => 255],
            [['UkrZed'], 'string', 'max' => 13],
            [['CheeseFat'], 'string', 'max' => 9],
            [['Fats', 'Carbs', 'Proteins', 'Sugar', 'CaloricValue', 'PercentageOfAlcohol'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recognize_gtin' => 'Recognize Gtin',
            'average_rating' => 'Average Rating',
            'nameUk' => 'Name Uk',
            'nameEn' => 'Name En',
            'nameRu' => 'Name Ru',
            'DescriptionTextRu' => 'Description Text Ru',
            'DescriptionTextUk' => 'Description Text Uk',
            'DescriptionTextEn' => 'Description Text En',
            'ManufacturerNameRu' => 'Manufacturer Name Ru',
            'ManufacturerNameUk' => 'Manufacturer Name Uk',
            'ManufacturerNameEn' => 'Manufacturer Name En',
            'CountryOfOrigin' => 'Country Of Origin',
            'FunctionalNameTextRu' => 'Functional Name Text Ru',
            'FunctionalNameTextUk' => 'Functional Name Text Uk',
            'FunctionalNameTextEn' => 'Functional Name Text En',
            'AdditionalTradeItemDescriptionRu' => 'Additional Trade Item Description Ru',
            'AdditionalTradeItemDescriptionUk' => 'Additional Trade Item Description Uk',
            'AdditionalTradeItemDescriptionEn' => 'Additional Trade Item Description En',
            'UkrZed' => 'Ukr Zed',
            'CheeseFat' => 'Cheese Fat',
            'GeneticallyModified' => 'Genetically Modified',
            'CaloricValue' => 'Caloric Value',
            'Fats' => 'Fats',
            'Carbs' => 'Carbs',
            'Proteins' => 'Proteins',
            'PercentageOfAlcohol' => 'Percentage Of Alcohol',
            'Sugar' => 'Sugar',
            'MinimumLifespanFromArrival' => 'Minimum Lifespan From Arrival',
            'MinimumLifespanFromProduction' => 'Minimum Lifespan From Production',
            'StorageHandlingTemperatureMaximum' => 'Storage Handling Temperature Maximum',
            'StorageHandlingTemperatureMinimum' => 'Storage Handling Temperature Minimum',
            'BrandName' => 'Brand Name',
            'InformationProviderLogo' => 'Information Provider Logo',
            'created' => 'Created',
            'updated' => 'Updated',
            'Brick' => 'Category',
            'InformationProviderGLN' => 'Information Provider GLN',
        ];
    }

    public function gtin_verification($gtin, $returnProduct = false){
        $products = Product::find()->where('recognize_gtin = :recognize_gtin', [':recognize_gtin' => $gtin])->one();
        $result = 'In base!';
        if(empty($products)){
            $result = $this->getProductFromServer($gtin);
        }
        if($returnProduct) {
            if(isset($data) && isset($data->error)) {
                $result = $data->error;
            } else {
                $history_model = new UserHistory();
                $images_model = new ProductImage();
                $values = $this->find_gtin($gtin);
                $product_id = $this->find_id($gtin);
                if(!empty($values) && is_array($values)){
                    $values['images'] = $images_model->find_images($product_id);
                    $values['votes'] = $history_model->find_votes_by_product($gtin);
                    $result = $values;
                } else {
                    $result = 'Undefined GTIN!';
                }
            }
        }
        return $result;
    }


    private function getProductFromServer($gtin, $new = true){
        $result = '';
        $url = Yii::$app->params['ucatApiUrl'] . '/products/' . $gtin . '/search?authKey=' . Yii::$app->params['authKey'];
        $data = $this->curlRequest($url);
        if(!isset($data->error) && !empty($data)){
            $logo = "";
            $product = $this->getProduct($data, $gtin);
            if(!empty($product) && $product instanceof stdClass)
            {
                if(!empty($product->InformationProviderLogo)) {
                    $picture = file_get_contents($product->InformationProviderLogo);
                    $logo = base64_encode($picture);
                }
                $values = [
                    'recognize_gtin' => $product->GTIN,
                    'average_rating' => NULL,
                    'DescriptionTextRu' => $product->DescriptionTextRu,
                    'DescriptionTextUk' => $product->DescriptionTextUk,
                    'DescriptionTextEn' => $product->DescriptionTextEn,
                    'ManufacturerNameRu' => $product->ManufacturerNameRu,
                    'ManufacturerNameUk' => $product->ManufacturerNameUk,
                    'ManufacturerNameEn' => $product->ManufacturerNameEn,
                    'CountryOfOrigin' => $product->CountryOfOrigin,
                    'FunctionalNameTextRu' => $product->FunctionalNameTextRu,
                    'FunctionalNameTextUk' => $product->FunctionalNameTextUk,
                    'FunctionalNameTextEn' => $product->FunctionalNameTextEn,
                    'AdditionalTradeItemDescriptionRu' => $product->AdditionalTradeItemDescriptionRu,
                    'AdditionalTradeItemDescriptionUk' => $product->AdditionalTradeItemDescriptionUk,
                    'AdditionalTradeItemDescriptionEn' => $product->AdditionalTradeItemDescriptionEn,
                    'UkrZed' => $product->UkrZed,
                    'CheeseFat' => $product->CheeseFat,
                    'GeneticallyModified' => $product->GeneticallyModified,
                    'CaloricValue' => $product->CaloricValue,
                    'Fats' => $product->Fats,
                    'Carbs' => $product->Carbs,
                    'Proteins' => $product->Proteins,
                    'PercentageOfAlcohol' => $product->PercentageOfAlcohol,
                    'Sugar' => $product->Sugar,
                    'MinimumLifespanFromArrival' => $product->MinimumLifespanFromArrival,
                    'MinimumLifespanFromProduction' => $product->MinimumLifespanFromProduction,
                    'StorageHandlingTemperatureMaximum' => $product->StorageHandlingTemperatureMaximum,
                    'StorageHandlingTemperatureMinimum' => $product->StorageHandlingTemperatureMinimum,
                    'BrandName' => $product->BrandName,
                    'InformationProviderLogo' => $logo,
                    'Brick' => $product->Brick,
                    'InformationProviderGLN' => $product->InformationProviderGLN
                ];
                if($new) {
                    $product_model = new Product();
                } else {
                    $product_model = self::find()->where('recognize_gtin = :recognize_gtin', [':recognize_gtin' => $gtin])->one();
                    if(empty($product_model)){
                        $product_model = new Product();
                        $new = true;
                    }
                }
                if( $this->productSave($product_model, $values, $product->Images, $new) === false) {
                    $result = 'Invalid data!';
                }
            }else {
                $result = 'Invalid data!';
            }
        } else {
            $result = $data->error;
        }
        return $result;
    }

    private function productSave($product_model, $values, $image = [], $new = false) {
        $product_model->setAttributes($values);
        if($product_model->validate()){
            if(!$new) {
                ProductImage::deleteAll(['product_gtin' => $values['recognize_gtin']]);
            }
            $product_model->save();
            Categories::saveCountProducts();
            $images_model = new ProductImage();
            $images_data = ['product_id' => $product_model->id, 'product_gtin' => $values['recognize_gtin'], 'images' => $image];
            $result = $images_model->save_images($images_data);
        } else {
            $result = false;
        }
        return $result;
    }


    private function getProduct($data = array(), $gtin = 0, $result = false){
        if(isset($data[0]->GTIN) && $data[0]->GTIN == $gtin) {
            $result = $data[0];
        } else {
            foreach ($data as $_data)
            {
                $item = $_data;
                while(!empty($item->ChildGTIN))
                {
                    if(isset($item->ChildGTIN->GTIN) && $item->ChildGTIN->GTIN == $gtin)
                    {
                        $result = $item->ChildGTIN;
                    }
                    $item = $item->ChildGTIN;
                }
            }
        }
        return $result;
    }

    public function save_average_rating($gtin){
        $ratings = UserHistory::find()->select('rating')->where('gtin = :gtin', [':gtin' => $gtin])->andWhere('action = "voted"')->asArray()->all();
        $ratings_array = array();
        foreach ($ratings as $rating){
            $ratings_array[] = $rating['rating'];
        }
        $product = Product::find()->where('recognize_gtin = :recognize_gtin', [':recognize_gtin' => $gtin])->one();
        $product->average_rating = round(array_sum($ratings_array)/count($ratings_array), 1);
        $product->save(FALSE);
    }

    public function ratings_list($gtins){
        $products = Product::find()->select(['recognize_gtin', 'average_rating'])->andWhere(['recognize_gtin' => $gtins])->asArray()->all();
        return (json_encode($products));
    }

    public function find_gtin($gtin, $feed = false) {
        if ($feed) {
            $fields = ['recognize_gtin',
                'average_rating',
                'DescriptionTextRu',
                'DescriptionTextUk',
                'DescriptionTextEn',
                'ManufacturerNameRu',
                'ManufacturerNameUk',
                'ManufacturerNameEn',
                'BrandName',
            ];
        } else {
            $fields = ['recognize_gtin',
                'average_rating',
                'DescriptionTextRu',
                'DescriptionTextUk',
                'DescriptionTextEn',
                'ManufacturerNameRu',
                'ManufacturerNameUk',
                'ManufacturerNameEn',
                'CountryOfOrigin',
                'FunctionalNameTextRu',
                'FunctionalNameTextUk',
                'FunctionalNameTextEn',
                'AdditionalTradeItemDescriptionRu',
                'AdditionalTradeItemDescriptionUk',
                'AdditionalTradeItemDescriptionEn',
                'UkrZed',
                'CheeseFat',
                'GeneticallyModified',
                'CaloricValue',
                'Fats',
                'Carbs',
                'Proteins',
                'PercentageOfAlcohol',
                'Sugar',
                'MinimumLifespanFromArrival',
                'MinimumLifespanFromProduction',
                'StorageHandlingTemperatureMaximum',
                'StorageHandlingTemperatureMinimum',
                'BrandName',
                'InformationProviderLogo',
                'Brick',
                'InformationProviderGLN'
            ];
        }
        $product = Product::find()->select($fields)
            ->andWhere(['recognize_gtin' => $gtin])->asArray()->one();
        if(empty($product)){
            $product = 'Undefined GTIN!';
        }
        return $product;
    }

    public function find_id($gtin){
        $id = $this->find()->select('id')->where('recognize_gtin = :recognize_gtin', [':recognize_gtin' => $gtin])->asArray()->one();
        return $id['id'];
    }

    public function get_product_manufacturer($post, $all = false, $return_products = true){
        $page = isset($post['page'])?$post['page']-1:0;
        $productsResult = array();
        $recognize_gtins = Product::find()->select('recognize_gtin')->where('InformationProviderGLN = :InformationProviderGLN', [':InformationProviderGLN' => $post['gln']])->asArray()->all();
        if (!$recognize_gtins || !is_array($recognize_gtins) || empty($recognize_gtins)) {
            return ['error' => 'Products not found.', 'parameter' => 'gln', 'code' => '200'];
        }
        $data= array();
        foreach ($recognize_gtins as $_recognize_gtin){
            $data[] = $_recognize_gtin['recognize_gtin'];
        }
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => ($all) ? 9999999999 : Yii::$app->params['pages'],
                'page' => $page,
            ],
        ]);
        $products = $provider->getModels();
        foreach ($products as $_products) {
            $gtin_verification = $this->gtin_verification($_products, $return_products);
            if (!isset($gtin_verification['error']) && $gtin_verification != 'Undefined GTIN!') {
                $productsResult[] = $gtin_verification;
            }
        }
        $productsResult['totalPages'] = (int)((count($data) + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
        return $productsResult;

    }

    /**
     * @param $post
     * @return array
     */
    public function create_product($post) {
        $model = new Product();
        $images = $this->save_images($post);
        if($images === false) {
            return ['error' => 'Photo of product was not saved. Try again later', 'parameter' => 'images', 'code' => '200'];
        }
        $productSave = $model->productSave($model, $post, $images);
        if($productSave === false) {
            return  ['error' => 'Product was not saved. Try again later', 'parameter' => 'images', 'code' => '200'];
        }
        return ['data' => ['success' => 'Product was created!'], 'message' => 'success', 'status' => '200'];
    }

    private function save_images($post, $images = []) {
        if(!empty($post['images'])) {
            $images = json_decode($post['images']);
            if(is_array($images) && !empty($images)) {
                if(!is_dir('images')){
                    mkdir('images');
                }
                foreach ($images as $key=>$image) {
                    if(!empty($image) && $image instanceof stdClass) {
                        $decoded = base64_decode(str_replace(' ', '+', $image->image));
                        $fileName = $post['recognize_gtin'] . '_' . sprintf('%04d', rand(0, 9999)) . '_' . time() . '.png';
                        $filePath = Url::to('@web/images/' . $fileName, 'https');
                        $file_put_contents = file_put_contents( Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $fileName,$decoded);
                        if ( $file_put_contents === false ){
                            return false;
                        }
                        $images[$key]->name = $fileName;
                        $images[$key]->link = $filePath;
                        $images[$key]->isPlanogram = (isset($image->isPlanogram))? $image->isPlanogram:0;
                        unset($images[$key]->image);
                    }
                }
            }
        }
        return $images;
    }

    /**
     * @param $post
     * @return array
     *
     */
    public function get_product_category($post){
        $items = Yii::$app->params['pages'];
        $offset = isset($post['page'])?$post['page']*$items-$items:0;
        $categoryId = isset($post['category'])?$post['category']:0;
        $products = Product::find()->select(['*'])->andWhere(['Brick' => $categoryId])->offset($offset)->limit($items)->orderBy('created desc')->asArray()->all();
        $productsTotal = Product::find()->select(['*'])->andWhere(['Brick' => $categoryId])->count();
        $productsResult = array();
        if(!empty($products) && is_array($products)) {
            foreach ($products as $_products) {
                $gtin_verification = $this->gtin_verification($_products['recognize_gtin'], true);
                if (!isset($gtin_verification['error'])) {
                    $productsResult[] = $gtin_verification;
                }
            }
            $productsResult['totalPages'] = (int)(($productsTotal + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
            //$productsResult['categoryName'] = Categories::get_name($categoryId);
        }
        else {
            $productsResult =  ['message' => 'Products not found.'];
        }
        return $productsResult;
    }


    public function save_InformationProviderGLN(){
        $products = self::find()->select(['id', 'InformationProviderGLN', 'recognize_gtin'])->where('InformationProviderGLN Is Null')->asArray()->all();
        foreach ($products as $_products) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] . '/products/' . $_products['recognize_gtin'] . '/search?authKey=' . Yii::$app->params['authKey']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output);
            if(!isset($data->error)){
                $product = $this->getProduct($data, $_products['recognize_gtin']);
                if(!empty($product->InformationProviderGLN)) {
                    $model = self::find()->where(['id' => $_products['id']])->one();
                    $model->InformationProviderGLN = $product->InformationProviderGLN;
                    $model->save('InformationProviderGLN');
                }
            }
        }
    }


    public function count_products($gln, $allProducts){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] . '/manufacturers/' . $gln . '/products?authKey=' . Yii::$app->params['authKey']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);

        if(!isset($data->error)) {
            foreach ($data as $_products) {
                $allProducts[$_products] = $_products;
            }
        }
        return $allProducts;

    }

    public function count_all_products(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] . '/manufacturers?authKey=' . Yii::$app->params['authKey']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);
        $allProducts = array();
        foreach ($data as $key => $_data)
        {
            $post['gln'] = $_data->primaryGLN;
            $allProducts = $this->count_products($_data->primaryGLN, $allProducts);
        }
        return count($allProducts);
    }

    public function save_all_product(){
        //$this->save_InformationProviderGLN();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['ucatApiUrl'] . '/manufacturers?authKey=' . Yii::$app->params['authKey']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);
        foreach ($data as $key => $_data)
        {
            $post['gln'] = $_data->primaryGLN;
            $this->get_product_manufacturer($post, true, false);
            sleep(0.2);
        }
    }

    public function last_updated_gtins(){
        $url = Yii::$app->params['ucatApiUrl'] . '/last-changed-gtins/?authKey=' . Yii::$app->params['authKey'];
        $data = $this->curlRequest($url);
        foreach ($data as $_data) {
            if(!empty($_data->newGTINs) && is_array($_data->newGTINs)) {
                foreach ($_data->newGTINs as $GTIN) {
                    $this->getProductFromServer($GTIN, false);
                }
            }
        }
    }

    public function curlRequest($url = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);

        return $data;
    }

}
