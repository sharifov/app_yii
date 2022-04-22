<?php

namespace modules\sales\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_units".
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $quantity_divider Is the coefficient for quantity value. Value in the database will be
 * divided by this divider to be displayed for user. This allow us to store only integer values in the database.
 *
 * @property Product[] $products
 */
class Unit extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_units}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Единица измерения';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Единицы измерения';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'string', 'max' => 255],
            ['name', 'required'],
            ['short_name', 'required'],
            ['quantity_divider', 'required'],
            ['quantity_divider', 'integer', 'min' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'short_name' => 'Короткое обозначение',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'quantity_divider' => 'Делитель количества',
        ];
    }

    public function attributeHints()
    {
        return [
            'quantity_divider' => 'Значения количества в базе хранятся в виде долей целого, определяя минимально возможную единицу.
            Для того, чтобы пользователи могли вводить значения в виде целого с дробной частью, используется делитель.
            Например, если товар измеряется в метрах, а участник должен иметь возможность указывать количество
            вплоть до сотой доли метра, следует ввести делитель 100. Тогда при вводе участником 5.25, в базе значение будет
            сохранено как 525 (в сотых долях), но везде на сайте отображаться будет именно 5.25. При этом указать например
            5.256 участник не сможет, т.е. после умножения до 100 результат будет округлен вниз до целого и как результат, значение
            в базе будет все равно 5.25.',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['unit_id' => 'id']);
    }
}
