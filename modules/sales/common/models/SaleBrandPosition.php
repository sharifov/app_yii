<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_brand_positions".
 *
 * @property integer $id
 * @property integer $sale_id
 * @property integer $brand_id
 * @property integer $kg
 * @property integer $rub
 * @property float $kg_real
 * @property integer $bonuses
 *
 * @property Sale $sale
 * @property Brand $brand
 */
class SaleBrandPosition extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_brand_positions}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Brand Position';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Brand Positions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sale_id', 'integer'],
            ['brand_id', 'integer'],
            ['kg', 'integer'],
            ['rub', 'integer'],
            ['bonuses', 'integer'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sale_id' => 'Sale ID',
            'brand_id' => 'Brand ID',
			'bonuses' => 'Бонусы',
            'rub' => 'Продано на сумму',
			'kg' => 'кг * 100',
			'kg_real' => 'кг',
        ];
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['id' => 'product_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSale()
	{
		return $this->hasOne(Sale::className(), ['id' => 'sale_id']);
	}

	public function getKg_real()
	{
		return $this->kg / 100;
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }
}
