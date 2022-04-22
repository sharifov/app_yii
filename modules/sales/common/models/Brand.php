<?php

namespace modules\sales\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_brands".
 *
 * @property integer $id
 * @property string $name
 * @property integer $x
 * @property integer $x_real
 * @property integer $xprof
 *
 * @property Product[] $products
 */
class Brand extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /** @var string */
    protected $x_real;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_brands}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Бренд продукции';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Бренды продукции';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            ['name', 'required'],
            ['x', 'integer'],
			['xprof', 'integer'],
            ['x_real', 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Бренд',
            'x' => 'Коэффициент * 100',
            'x_real' => 'Коэффициент для акции "Для персонала дилера"',
			'xprof' => 'Коэфициент для акции "Профессиональный подход"',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['brand_id' => 'id']);
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

    public static function getOptions()
    {
        return self::find()->indexBy('id')->select(['name', 'id'])->column();
    }
}
