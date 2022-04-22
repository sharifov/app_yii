<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;
use modules\profiles\common\models\Dealer;
use yii\behaviors\TimestampBehavior;
use marketingsolutions\datetime\DateTimeBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "kr_sales".
 *
 * @property integer $id
 * @property string $status
 * @property integer $kg
 * @property integer $bonuses
 * @property string $created_at
 * @property string $updated_at
 * @property string $sold_on
 * @property string $approved_by_admin_at
 * @property string $bonuses_paid_at
 * @property integer $dealer_id
 * @property integer $previous_kg
 * @property integer $x
 * @property integer $xx
 * @property string $rule
 * @property integer $manager_commission
 * @property integer $manager_commission_included
 * @property integer $manager_bonuses
 * @property integer $dealer_bonuses
 * @property integer $promotion_id
 * @property integer $rub
 *
 * @property SalesPromotions $promotion
 * @property SalesPositions[] $salesPositions
 */
class CreateSale extends Sale implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Создать продажу';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Создание продаж';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(),
                'attributes' => [
                    'sold_on' => [
                        'targetAttribute' => 'sold_on_local',
                        'originalFormat' => ['date', 'yyyy-MM-dd'],
                        'targetFormat' => ['date', 'dd.MM.yyyy'],
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'string', 'max' => 16],
            ['kg', 'integer'],
            ['bonuses', 'integer'],
            ['created_at', 'safe'],
            ['updated_at', 'safe'],
            ['sold_on_local', 'safe'],
            [['sold_on_local', 'dealer_id'], 'required'],
            ['approved_by_admin_at', 'safe'],
            ['bonuses_paid_at', 'safe'],
            ['dealer_id', 'integer'],
            ['previous_kg', 'integer'],
            ['x', 'integer'],
            ['xx', 'integer'],
            ['rule', 'string', 'max' => 255],
            ['manager_commission', 'integer'],
            ['manager_commission_included', 'string', 'max' => 1],
            ['manager_bonuses', 'integer'],
            ['dealer_bonuses', 'integer'],
            ['promotion_id', 'integer'],
            ['rub', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status' => 'Статус',
            'kg' => 'Продано единиц (шт или кг) * 100',
            'bonuses' => 'Бонусы',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'sold_on' => 'Дата продажи',
            'sold_on_local' => 'Дата продажи',
            'approved_by_admin_at' => 'Дата одобрения администратором',
            'bonuses_paid_at' => 'Дата начисления бонусов',
            'dealer_id' => 'Дилер',
            'previous_kg' => 'Продажа за 2014 год, кг * 100',
            'x' => 'x * 100',
            'xx' => 'xx * 100',
            'rule' => 'Правило бонусов',
            'manager_commission' => 'Бонусы руководителю ДЦ в %',
            'manager_commission_included' => 'Бонусы руководителю ДЦ включается в бонусы от продажи',
            'manager_bonuses' => 'Бонусы руководителю ДЦ',
            'dealer_bonuses' => 'Бонусы дилеру',
            'promotion_id' => 'Акция',
            'rub' => 'Проданно на сумму, руб.',
        ];
    }

    /**
     * @return array
     */
    public function getDealers()
    {
        return Dealer::find()->indexBy('id')->select(['name', 'id'])->column();
    }

    /**
     * @return array
     */
    public function getPromotions()
    {
        return Promotion::find()->indexBy('id')->select(['name', 'id'])->column();
    }
}
