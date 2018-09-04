<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "userDevice".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $token
 * @property string $lastLogin
 * @property integer $production
 * @property string $timezone
 * @property string $application
 * @property string $userToken
 * @property string $created
 */
class UserDevice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userDevice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'application'], 'required'],
            [['userId', 'production'], 'integer'],
            [['token', 'application', 'userToken'], 'string', 'max' => 255],
            [['lastLogin', 'created'], 'string', 'max' => 30],
            [['timezone'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'token' => 'Token',
            'lastLogin' => 'Last Login',
            'production' => 'Production',
            'timezone' => 'Timezone',
            'application' => 'Application',
            'userToken' => 'User Token',
            'created' => 'Created',
        ];
    }
}
