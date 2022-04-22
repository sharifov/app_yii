<?php

namespace modules\sales\common\models;

use modules\sales\common\sales\bonuses\UnitsConverter;
use Yii;
use yz\admin\models\User;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_positions".
 *
 * @property integer $id
 * @property integer $sale_id
 * @property integer $product_id
 * @property integer $kg
 * @property float $kg_real
 * @property integer $bonuses
 *
 * @property Sale $sale
 * @property Product $product
 */
class SalePosition extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    const TYPE_PACKING = 'packing';
    const TYPE_KG = 'kg';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_positions}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Sale Position';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Sale Positions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['product_id', 'required', 'message' => 'Не выбран товар'],
            ['product_id', 'in', 'range' => array_keys(self::getProductIdValues()), 'message' => 'Выбранный товар не доступен'],
            ['kg', 'required'],
        ];
    }

    public static function getProductIdValues()
    {
        return Product::find()->select('name, id')->indexBy('id')->column();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sale_id' => 'Sale ID',
            'product_id' => 'Product ID',
            'bonuses' => 'Бонусы',
            'kg' => 'кг * 100',
            'kg_real' => 'кг',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'sale_id',
            'product_id',
            'kg',
            'kg_real',
            'bonuses',
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
}
