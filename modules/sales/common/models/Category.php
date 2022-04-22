<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_categories".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Product[] $products
 */
class Category extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_categories}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Вид продукции';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Виды продукции';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Вид',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }
}
