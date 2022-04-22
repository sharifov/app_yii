<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_promotion_rules".
 *
 * @property integer $id
 * @property integer $promotion_id
 * @property integer $priority
 * @property string $condition
 * @property string $rule
 * @property string $description
 *
 * @property Promotion $promotion
 */
class PromotionRule extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_promotion_rules}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Правило начисления баллов по акции';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Правила начисления баллов по акциям';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['promotion_id', 'condition', 'rule'], 'required'],
            ['promotion_id', 'integer'],
            ['priority', 'integer'],
            ['condition', 'string', 'max' => 255],
            ['rule', 'string', 'max' => 255],
            ['description', 'string', 'max' => 255],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotion::className(), 'targetAttribute' => ['promotion_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Акция',
            'priority' => 'Приоритет',
            'condition' => 'Условие',
            'rule' => 'Формула для подсчетов',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
    }
}
