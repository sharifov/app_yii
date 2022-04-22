<?php

namespace modules\sales\common\models;

use modules\sales\common\sales\bonuses\BonusesCalculator;
use modules\sales\common\sales\bonuses\FormulaValidator;
use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_products".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $type_id
 * @property integer $brand_id
 * @property string $name
 * @property integer $packing Фасовка товара, умноженная на 100
 * @property integer $packingReal Фасовка товара
 *
 * @property Category $category
 * @property Type $type
 * @property Brand $brand
 */
class Product extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_products}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Товар';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Продукция';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'type_id', 'brand_id', 'packing'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['category_id', 'required'],
            ['type_id', 'required'],
            ['brand_id', 'required'],
            ['name', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Вид',
            'type_id' => 'Тип',
            'brand_id' => 'Бренд',
            'name' => 'Название',
            'packing' => 'Фасовка',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    public static function getCategoryIdValues()
    {
        return Category::find()->select('name, id')->indexBy('id')->column();
    }

    public static function getTypeIdValues()
    {
        return Type::find()->select('name, id')->indexBy('id')->column();
    }

    public static function getBrandIdValues()
    {
        return Brand::find()->select('name, id')->indexBy('id')->column();
    }

    public function getPackingReal()
    {
        return $this->packing / 100;
    }
}
