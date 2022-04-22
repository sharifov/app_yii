<?php

namespace modules\sales\common\models;

use modules\profiles\common\models\Dealer;
use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_factors".
 *
 * @property integer $id
 * @property integer $x
 * @property integer $x_real
 * @property integer $dealer_id
 * @property integer $brand_id
 *
 * @property Brand $brand
 * @property Dealer $dealer
 */
class Factor extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /** @var string */
    protected $x_real;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_factors}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Коэффициент';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Коэффициенты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['x', 'dealer_id', 'brand_id'], 'integer'],
            [['dealer_id', 'brand_id'], 'required'],
            ['x_real', 'number'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
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
            'dealer_id' => 'Дилер',
            'brand_id' => 'Бренд',
            'x' => 'Коэффициент * 100',
            'x_real' => 'Коэффициент'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }

    public function getX_real()
    {
        return $this->x / 100;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->x = intval(floatval($this->x_real) * 100);

            return true;
        }
        return false;
    }
}
