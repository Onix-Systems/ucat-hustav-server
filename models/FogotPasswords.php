<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fogot_passwords".
 *
 * @property integer $id
 * @property string $email
 * @property string $key
 * @property string $create
 */
class FogotPasswords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fogot_passwords';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['create'], 'safe'],
            [['email'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'key' => 'Key',
            'create' => 'Create',
        ];
    }
    
    public function saveKey($key){
        $this->setAttributes($key);
        if($this->validate()){
            $user = FogotPasswords::find()->where('email = :email', [':email' => $key['email']])->one();
            if(isset($user)){
                $user->key = $key['key'];
                $user->update();
            } else {
                $this->save();
            }
        }
    }

    public function drop_key($key){
        $row = FogotPasswords::find()->where('email = :email', [':email' => $key['email']])->one();
        if(isset($row)){
            return $row->delete();
        } else {
            return false;
        }
    }

    public function empty_key($key){
        $row = FogotPasswords::find()->where('email = :email', [':email' => $key['email']])->one();
        if(isset($row)){
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
}
