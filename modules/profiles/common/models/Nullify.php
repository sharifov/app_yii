<?php

namespace modules\profiles\common\models;

use marketingsolutions\finance\models\Purse;
use modules\sales\common\models\Sale;
use modules\sales\common\sales\statuses\Statuses;
use ms\loyalty\finances\common\components\CompanyAccount;
use Yii;
use yz\interfaces\ModelInfoInterface;
use marketingsolutions\finance\models\Transaction;
use ms\loyalty\bonuses\manual\common\finances\BackendUserPartner;


/**
 * This is the model class for table "kr_nullify".
 *
 * @property integer $id
 * @property integer $profile_id
 * @property integer $sum
 * @property string $created_at
 */
class Nullify extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%nullify}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Nullify';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'История обнуления баллов по участникам';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['profile_id', 'integer'],
            ['sum', 'integer'],
            ['created_at', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => 'Участник',
            'sum' => 'Сумма обнуления',
            'created_at' => 'Дата обнуления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

    /**
     * @return mixed
     */
    public function getProfileName()
    {
        return $this->profile->full_name;
    }

    /**
     * @return mixed
     */
    public function getProfileRole()
    {
        return $this->profile->role;
    }

    /**
     * @return mixed
     */
    public function getProfilePhone()
    {
        return $this->profile->phone_mobile;
    }

    /**
     * @return bool
     */
    public static function isNullify()
    {
        $notInStatus = [Statuses::PAID, Statuses::DECLINED];
        $sales = Sale::find()->select('id')->where(['not in' ,'status', $notInStatus ])->column();

        if(empty($sales)){
            return false;
        }

        return $sales;
    }

    public static function nullifySale()
    {
        $purses = Purse::find()
            ->select('owner_id, owner_type, balance')
            ->where(['is not', 'owner_id', new \yii\db\Expression('null')])
            ->andWhere(['owner_type' => Profile::className()])
            ->andWhere(['>', 'balance', 0])
            ->asArray()
            ->all();
        $now = date("d.m.Y");
        $nowInsert = date("Y-m-d H:i:s");
        $arrInsert = [];
        $arr = [];
        foreach ($purses as $purse){
            $profile = Profile::findOne($purse['owner_id']);
            if($profile) {
                $purse = $profile->purse;
                $purse->addTransaction(Transaction::factory(
                    Transaction::OUTBOUND,
                    $purse['balance'],
                    new BackendUserPartner(['id' => $purse['owner_id']]),
                    "Списание баллов участника ".$profile->full_name." от ". $now
                ));
                $arr['profile_id'] = $purse['owner_id'];
                $arr['sum'] = $purse['balance'];
                $arr['created_at'] = $nowInsert;
                $arrInsert[]=$arr;
            }
        }
        \Yii::$app->db->createCommand()
            ->batchInsert(
                '{{%nullify}}',
                ['profile_id', 'sum', 'created_at'],
                $arrInsert
            )->execute();
        //Обнуляем дилеров и
        Purse::updateAll(['balance' => 0], ['<>', 'owner_type', CompanyAccount::className()]);
    }
}
