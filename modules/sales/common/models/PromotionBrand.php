<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_promotion_brands".
 *
 * @property integer $promotion_id
 * @property integer $brand_id
 * @property integer $x
 * @property float $x_real
 * @property integer $rub_percent
 * @property float $rub_percent_real
 *
 * @property Brand $brand
 * @property Promotion $promotion
 */
class PromotionBrand extends \yii\db\ActiveRecord implements ModelInfoInterface
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%sales_promotion_brands}}';
	}

	/**
	 * Returns model title, ex.: 'Person', 'Book'
	 *
	 * @return string
	 */
	public static function modelTitle()
	{
		return 'Promotion Brand';
	}

	/**
	 * Returns plural form of the model title, ex.: 'Persons', 'Books'
	 *
	 * @return string
	 */
	public static function modelTitlePlural()
	{
		return 'Promotion Brands';
	}

	public static function primaryKey()
	{
		return array('promotion_id', 'brand_id');
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['promotion_id', 'integer'],
			['brand_id', 'integer'],
			['x', 'integer'],
            ['rub_percent', 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'promotion_id' => 'Promotion ID',
			'brand_id' => 'Brand ID',
			'x' => 'X',
            'rub_percent' => 'Rub Percent'
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
	public function getPromotion()
	{
		return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
	}

	public function getX_real()
	{
		return $this->x / 100;
	}

    public function getRub_percent_real() {
        return $this->rub_percent / 100;
    }
}
