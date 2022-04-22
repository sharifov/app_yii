<?php

namespace modules\profiles\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_dealer_payments".
 *
 * @property integer $id
 * @property integer $bonuses
 * @property string $created_at
 * @property string $updated_at
 * @property integer $recipient_id
 * @property integer $dealer_id
 * @property integer $manager_id
 *
 * @property Dealer $dealer
 * @property Profile $manager
 * @property Profile $recipient
 */
class DealerPayment extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dealer_payments}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Перевод участнику от диллера';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Переводы участнику от диллера';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipient_id', 'dealer_id', 'manager_id', 'bonuses'], 'required'],
            ['created_at', 'safe'],
            ['updated_at', 'safe'],
            ['recipient_id', 'integer'],
            ['dealer_id', 'integer'],
            ['manager_id', 'integer'],
            ['bonuses', 'integer'],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealer::className(), 'targetAttribute' => ['dealer_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['manager_id' => 'id']],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['recipient_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'recipient_id' => 'ID получателя',
            'dealer_id' => 'ID дилера',
            'manager_id' => 'ID участника',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Profile::className(), ['id' => 'manager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(Profile::className(), ['id' => 'recipient_id']);
    }
}
