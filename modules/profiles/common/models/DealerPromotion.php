<?php

namespace modules\profiles\common\models;

use modules\sales\common\models\Promotion;
use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "kr_dealers_promotions".
 *
 * @property integer $id
 * @property integer $dealer_id
 * @property integer $promotion_id
 *
 * @property Promotion $promotion
 * @property Dealer $dealer
 */
class DealerPromotion extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dealers_promotions}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Dealer Promotion';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Dealer Promotions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['dealer_id', 'integer'],
            ['promotion_id', 'integer'],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotion::className(), 'targetAttribute' => ['promotion_id' => 'id']],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealer::className(), 'targetAttribute' => ['dealer_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealer_id' => 'Dealer ID',
            'promotion_id' => 'Promotion ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }
}
