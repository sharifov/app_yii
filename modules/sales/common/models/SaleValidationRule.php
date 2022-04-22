<?php

namespace modules\sales\common\models;

use modules\sales\common\sales\validation\RuleEvaluator;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sale_validation_rules".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_enabled
 * @property string $rule
 * @property string $error
 * @property integer $promotion_id
 * @property Promotion $promotion
 * @property RuleEvaluator $evaluator
 */
class SaleValidationRule extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sale_validation_rules}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Правило';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Правила проверки закупок';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['is_enabled', 'integer'],
			['promotion_id', 'integer'],
			['promotion_id', 'required'],
            ['name', 'string', 'max' => 128],
            ['name', 'required'],
            ['rule', 'string', 'max' => 255],
            ['rule', 'required'],
            ['rule', 'validateRule'],
            ['error', 'string', 'max' => 255],
        ];
    }

    public function validateRule()
    {
        try {
            (new RuleEvaluator($this))->evaluate(new Sale(), [], []);
        } catch (SyntaxError $e) {
            $this->addError('rule', 'Формула имеет неверный формат: '.$e->getMessage());
        } catch (ErrorException $e) {
            $this->addError('rule', 'При проверки формулы выявлена следующая ошибка: ' . $e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_enabled' => 'Задействовано',
            'rule' => 'Правило',
            'name' => 'Название',
            'error' => 'Сообщение об ошибке',
			'promotion_id' => 'Акция',
			'x' => 'Коэффициент X * 100',
			'x_real' => 'Коэффициент X',
        ];
    }

    /**
     * @var RuleEvaluator
     */
    private $_evaluator;

    public function getEvaluator()
    {
        if ($this->_evaluator === null) {
            $this->_evaluator = new RuleEvaluator($this);
        }

        return $this->_evaluator;
    }

	/** @return ActiveQuery */
	public function getPromotion()
	{
		return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
	}
}
