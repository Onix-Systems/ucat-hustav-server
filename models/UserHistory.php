<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $gtin
 * @property string $action
 * @property integer $rating
 * @property string $created
 */
class UserHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'rating'], 'integer'],
            [['created'], 'safe'],
            [['gtin', 'action'], 'string', 'max' => 14],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'gtin' => 'Gtin',
            'action' => 'Action',
            'rating' => 'Rating',
            'created' => 'Created',
        ];
    }

    public function verifyGtin($user_id, $gtin){
        $scanned_gtin = UserHistory::find()->where('user_id = :user_id', [':user_id' => $user_id])->andWhere('gtin = :gtin', [':gtin' => $gtin])->andWhere('action = "scanned"')->one();
        if($scanned_gtin == NULL && $this->validate()){
            $this->save();
            return 'GTIN was saved!';
        } else {
            $scanned_gtin->created = date('Y-m-d H:i:s');
            $scanned_gtin->update();
            return 'This GTIN is already saved!';
        }
    }

    public function save_rating($user_id, $gtin){
        $rating = UserHistory::find()->where('user_id = :user_id', [':user_id' => $user_id])->andWhere('gtin = :gtin', [':gtin' => $gtin])->andWhere('action = "voted"')->one();
        if (empty($rating)){
            $this->save();
            return 'Your vote was counted!';
        } else {
            $rating->rating = $this->getAttribute('rating');
            $rating->created = date('Y-m-d H:i:s');
            $rating->update();
            return 'Your vote was changed!';
        }
    }

    public function save_view($user_id, $gtin){
        $views = UserHistory::find()->where('user_id = :user_id', [':user_id' => $user_id])->andWhere('gtin = :gtin', [':gtin' => $gtin])->andWhere('action = "viewed"')->orderBy(['created' => SORT_DESC])->one();
        if(empty($views) || (strtotime($views->created)+60*60*24) < strtotime(date('Y-m-d H:i:s'))){
            $this->save();
            return 'You viewed this product!';
        } else if((strtotime($views->created)+60*60*24) > strtotime(date('Y-m-d H:i:s'))) {
            return 'Viewed today!';
        }
    }

    public function last_ratings($params) {
        $items = (isset($params['items']))? $params['items'] : Yii::$app->params['pages'];
        $offset = isset($params['page'])?$params['page']*$items-$items:0;
        if(isset($params['user_id'])){
            $last = UserHistory::find()->select(['id', 'gtin'])->where('user_id = :id', [':id' => $params['user_id']])->andWhere('action = "voted"')->offset($offset)->limit($items)->orderBy('created desc')->asArray()->all();
        } else {
            $last = UserHistory::find()->select(['id', 'gtin'])->where('action = "voted"')->offset($offset)->limit($items)->orderBy('created desc')->asArray()->all();
        }
        $result = empty($last)?NULL:$last;
        return $result;
    }
    
    public function last_ratings_count($user_id = false, $items = null) {
        if($user_id){
            $totalCount = UserHistory::find()->select('*')->where('user_id = :id', [':id' => $user_id])->andWhere('action = "voted"')->count('*');
        } else {
            $totalCount = UserHistory::find()->select('*')->where('action = "voted"')->count('*');
        }
        return $this->getPagesCount($totalCount, $items);
    }

    private function getPagesCount($totalCount, $items = null){
        if ($items) {
            $pages = (int) (($totalCount + $items - 1) / $items);
        }else {
            $pages = (int) (($totalCount + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
        }
        return $pages;
    }
    
    
    public function last_scan($params) {
        $items = $params['items'] = Yii::$app->params['pages'];
        $offset = isset($params['page'])?$params['page']*$items-$items:0;
        $history = UserHistory::find()->select(['gtin', 'created'])->where('user_id = :id', [':id' => $params['user_id']])->andWhere('action = "scanned"')->offset($offset)->limit($items)->orderBy('created desc')->asArray()->all();
        $result = empty($history)?NULL:$history;
        return $result;
    }
    
    public function get_last_scan_count($user_id) {
        $history = UserHistory::find()->select("*")->where('user_id = :id', [':id' => $user_id])->andWhere('action = "scanned"')->count("*");
        return $this->getPagesCount($history);
    }

    public function get_all_history($params) {
        $items = $params['items'] = Yii::$app->params['pages'];
        $offset = isset($params['page'])?$params['page']*$items-$items:0;
        $history = UserHistory::find()->select(['gtin', 'action', 'rating'])->where('user_id = :id', [':id' => $params['user_id']])->offset($offset)->limit($items)->orderBy('created desc')->asArray()->all();
        $result = empty($history)?NULL:$history;
        return $result;
    }
    
    public function get_all_history_count($user_id) {
        $totalCount = UserHistory::find()->select('*')->where('user_id = :id', [':id' => $user_id])->count('*');
        $result = (int) (($totalCount + Yii::$app->params['pages'] - 1) / Yii::$app->params['pages']);
        return $result;
    }

    public function find_votes_by_id($id){
        $query = UserHistory::find()->rightJoin('user', 'user.id = user_history.user_id')->select(['user.fullName', 'user.firstName', 'user.lastName', 'user.userSmallImage', 'user_history.rating', 'user_history.id', 'user_history.created']);
        $votes = $query->where('user_history.id = :id', [':id' => $id])->asArray()->one();
        unset($votes['id']);
        return [$votes];
    }

    public function find_votes_by_product($gtin){
        $query = UserHistory::find()->rightJoin('user', 'user.id = user_history.user_id')->select(['user.facebookId', 'user.fullName', 'user.firstName', 'user.lastName', 'user.userImage', 'user_history.rating', 'user_history.id', 'user_history.created']);
        $votes = $query->where('gtin = :gtin', [':gtin' => $gtin])->andWhere('action = "voted"')->orderBy('created desc')->asArray()->all();
        foreach ($votes as $key => $vote){
            unset($votes[$key]['id']);
        }
        return $votes;
    }

}
