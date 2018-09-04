<?php

namespace app\models;

use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $fullName
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $password
 * @property string $facebookId
 * @property string $twitterId
 * @property string $token
 * @property string $created
 * @property string $updated
 * @property string $userImage
 * @property string $userSmallImage
 * @property string $uAlfa2Code
 * @property integer $city
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_LOGOUT = 'logout';

    private $rememberMe = true;

    public $expiration = 10000;
    public $authKey;
    public $accessToken;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'email'], 'required', 'on' => [self::SCENARIO_REGISTER]],
            ['password', 'validatePassword', 'on' => self::SCENARIO_LOGIN],
            ['email', 'email'],
            ['email', 'unique'],
            [['fullName', 'email', 'password', 'token', 'firstName', 'lastName'], 'string', 'max' => 255],
            [['facebookId', 'twitterId'], 'string', 'max' => 32],
            [['created', 'updated'], 'string', 'max' => 30],
            [['userImage', 'userSmallImage', 'Alfa2Code'], 'string'],
            [['city'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullName' => 'Full Name',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'facebookId' => 'Facebook ID',
            'twitterId' => 'Twitter ID',
            'token' => 'Token',
            'created' => 'Created',
            'updated' => 'Updated',
            'userImage' => 'Image',
            'userSmallImage' => 'Small Image',
            'Alfa2Code' => 'Code of country',
            'city' => 'Id of cities',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city']);
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity ($id) {
        return self::find()->where(['email' => $id])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::find()->where(['token' => $token])->one();
        return $user ? $user : null;
    }

    /**
     * @inheritdoc
     */
    public function getId () {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey () {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey ($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     * @param $attribute
     */
    public function validatePassword ($attribute) {
        $user = User::find()->where(['email' => $this->email])->one();
        if (!$user || !Yii::$app->getSecurity()->validatePassword($this->password, $user->getAttribute('password'))) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @return bool
     */
    public function beforeSave ($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
                $this->created = date('c');
            }
            $this->updated = date('c');
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields['userDevices'] = 'userDevices';
        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDevices()
    {
        return $this->hasMany(UserDevice::className(), ['userId' => 'id']);
    }

    /**
     * @return bool|string
     */
    public function register () {
        $this->scenario = self::SCENARIO_REGISTER;
        if ($this->save()) {
            return $this->token;
        } else {
            return false;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login () {
        $this->scenario = self::SCENARIO_LOGIN;
        if ($this->validate()) {
            $user = User::find()->where(['email' => $this->email])->one();
            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function check_token($token){
        $user = $this->findByToken($token);
        if(!empty($user) && !empty($token)){
            return $user;
        } else {
            return 'Invalid token!';
        }
    }

    public static function findByToken($token)
    {
        $user = User::find()->where('token = :token', [':token' => $token])->one();
        return $user;
    }

    /**
     * @param $params
     * @return array
     */
    public function forgot_password($params){
        if(empty($params['email'])) {
            return null;
        }
        $user = User::findByEmail($params['email']);
        if(empty($user)) {
            return null;
        }
        $forgotKey = md5(microtime() . $user->getAttribute('password'));
        $link = Yii::$app->getRequest()->getHostInfo() . '/forgot-password?token='.$forgotKey.'&email='.$params['email'];
        $mail = Yii::$app->mailer->compose('@app/mail/layouts/html', ['content' => ''])
            ->setFrom('hustavapp@gmail.com')
            ->setTo($params['email'])
            ->setSubject('forgot password')
            ->setHtmlBody('If you forgot your password or need it to be reset, you can click the link below to reset it . 
            <a href="' . $link . '">Change password</a>.');
        $mail->send();
        return array('email' => $user->getAttribute('email'), 'key' => $forgotKey);
    }

    /**
     * @param $email
     * @return array|null|ActiveRecord
     */
    public static function findByEmail($email)
    {
        $user = User::find()->andWhere('email = :email', ['email' => $email])->one();
        return $user;
    }

    /**
     * @return bool|false|int
     * @throws \Exception
     * @throws Throwable
     * @throws \yii\base\Exception
     */
    public function newPass(){
        $result = false;
        $post = Yii::$app->request->post();
        if(!empty($post['password']) && !empty($post['email']) && !empty($post['token'])) {
            $key = FogotPasswords::find()->where('`email`=:email AND `key`=:key',  [':email' => $post['email'], ':key' => $post['token']])->one();
            if(empty($key)){
                return FALSE;
            }
            $user = User::find()->where('`email`=:email', [':email' => $post['email']])->one();
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($post['password']);
            $result = $user->update('password');
        }
        return $result;
    }

}
