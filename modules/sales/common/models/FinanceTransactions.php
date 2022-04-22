<?php

namespace modules\sales\common\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use marketingsolutions\finance\models\Purse;
use Yii;
use yz\admin\models\User;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_finance_transactions".
 *
 * @property integer $id
 * @property integer $purse_id
 * @property string $type
 * @property integer $balance_before
 * @property integer $amount
 * @property integer $balance_after
 * @property string $partner_type
 * @property integer $partner_id
 * @property integer $admin_id
 * @property string $title
 * @property string $comment
 * @property string $created_at
 * @property Purse $pursesOwner
 *
 * @property YzFinancePurses $purse
 */
class FinanceTransactions extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%finance_transactions}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Finance Transactions';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Начисления / списания';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['purse_id', 'integer'],
            ['admin_id', 'integer'],
            ['type', 'string'],
            ['balance_before', 'integer'],
            ['amount', 'integer'],
            ['balance_after', 'integer'],
            ['partner_type', 'string', 'max' => 255],
            ['partner_id', 'integer'],
            ['title', 'string', 'max' => 128],
            ['comment', 'string', 'max' => 255],
            ['created_at', 'safe'],
            ['purse_id', 'ineger'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purse_id' => 'Purse ID',
            'type' => 'Операция',
            'balance_before' => 'Balance Before',
            'amount' => 'Сумма',
            'balance_after' => 'Balance After',
            'partner_type' => 'Partner Type',
            'partner_id' => 'Partner ID',
            'title' => 'Title',
            'comment' => 'Comment',
            'created_at' => 'Дата и время операции',
        ];
    }



    public function getAdmin(){
        return $this->hasOne(User::className(), ['id' =>'admin_id']);
    }

    public function getAdminName(){
        return $this->admin->name;
    }

    public function getPursesOwner(){
        return $this->hasOne(Purse::class, ['id' => 'purse_id']);
    }

    public function getOwnerType(){
        return $this->pursesOwner->owner_type;
    }

    public function getOwnerId(){
        return $this->pursesOwner->id;
    }


    public function getProfileTable(){
        if($this->ownerType == 'modules\profiles\common\models\Profile') {
            return $this->hasOne(Profile::className(), ['id' => 'owner_id'])
                ->via('pursesOwner');
        }
    }
    public function getProfileTableName(){
        if($this->ownerType == 'modules\profiles\common\models\Profile') {
            return $this->profileTable->full_name;
        }else {
            return "";
        }
    }

    public function getProfileTablePhone(){
        if($this->ownerType == 'modules\profiles\common\models\Profile') {
            return $this->profileTable->phone_mobile;
        }else {
            return "";
        }
    }

    public function getDealerTable(){
        if($this->ownerType == 'modules\profiles\common\models\Dealer') {
            return $this->hasOne(Dealer::className(), ['id' => 'owner_id'])
                ->via('pursesOwner');
        }else {
            return "";
        }


    }

    public function getDealerTableName(){
        if($this->ownerType == 'modules\profiles\common\models\Dealer') {
            return $this->dealerTable->name;
        }else {
            return "";
        }
    }

    public function getPromoTable(){
        if($this->ownerType == 'modules\profiles\common\models\Dealer') {
            return $this->hasOne(Promotion::className(), ['id' => 'promotion_id'])
                ->via('pursesOwner');
        }else {
            return "";
        }
    }
    public function getPromoTableName(){
        if($this->ownerType == 'modules\profiles\common\models\Dealer') {
            return $this->promoTable->name;
        }else{
            return "";
        }
    }

}
