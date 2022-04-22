<?php

namespace modules\Sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_promotion_products".
 *
 * @property integer $id
 * @property integer $promotion_id
 * @property integer $product_id
 * @property integer $x
 * @property float $x_real
 *
 * @property Product $product
 * @property Promotion $promotion
 */
class PromotionProduct extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_promotion_products}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Promotion Product';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Promotion Products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['promotion_id', 'integer'],
            ['product_id', 'integer'],
            ['x', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Promotion ID',
            'product_id' => 'Product ID',
            'x' => 'X',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(\modules\sales\common\models\Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(\modules\sales\common\models\Promotion::className(), ['id' => 'promotion_id']);
    }

    public function getX_real() {
        return $this->x / 100;
    }
}
