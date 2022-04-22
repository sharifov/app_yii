<?php

namespace modules\sales\common\app\forms;

use Carbon\Carbon;
use frontend\base\Application;
use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use modules\sales\common\models\SaleBrandPosition;
use modules\sales\common\models\SaleDocument;
use modules\sales\common\models\SalePosition;
use modules\sales\common\models\SaleValidationRule;
use modules\sales\common\sales\statuses\Statuses;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class SaleEditForm
 */
class SaleEditForm extends Model
{
	/**
	 * @var string
	 */
	public $status;
	/**
	 * @var Sale
	 */
	public $sale;
	/**
	 * @var SalePosition[]
	 */
	public $positions;
    /**
     * @var SaleBrandPosition[]
     */
    public $brandPositions;
	/**
	 * @var bool
	 */
	public $useValidationRules = true;
	/**
	 * @var integer
	 */
	public $dealer_id = null;
	/**
	 * @var integer
	 */
	public $promotion_id = null;
    /**
     * @var SaleDocument[]
     */
    public $documents = [];

    /**
     * @var SaleDocument[]
     */
    private $newDocuments = [];

	public function __construct(Sale $sale, $config = [])
	{
		$this->sale = $sale;

		parent::__construct($config);
	}

	public function init()
	{
		if ($this->sale->isNewRecord) {
			$this->sale->sold_on = Carbon::today()->toDateString();
		} else {
		    $this->newDocuments = $this->documents;
            $this->documents = $this->sale->documents;
        }

		$this->positions = $this->sale->positions;
        $this->brandPositions = $this->sale->brandPositions;
	}

	public function fields()
	{
		return [
			'sale',
			'positions',
            'brandPositions',
            'documents',
		];
	}

	public function rules()
	{
		return [
			['status', 'required'],
			['status', 'in', 'range' => [Statuses::DRAFT, Statuses::ADMIN_REVIEW]]
		];
	}

    public function afterValidate()
    {
        parent::afterValidate();

        if (count($this->positions) == 0 && count($this->brandPositions) == 0) {
            $this->addError('positions', 'Вы должны добавить хотя бы одну позицию по продаже');
        }

        if (Yii::$app->getModule('sales')->documentsRequired) {
            if (count($this->documents) == 0) {
                $this->addError('documents', 'Вы должны добавить хотя бы один подтверждающий документ');
            }
        }

        if (Yii::$app->getModule('sales')->useValidationRules) {
            /** @var SaleValidationRule[] $validationRules */
            $validationRules = SaleValidationRule::find()->where('promotion_id = 3')->all();

            foreach ($validationRules as $rule) {
                if ($rule->evaluator->evaluate($this->sale, $this->positions, $this->documents) == true) {
                    continue;
                }

                $this->addError('sale', $rule->error);
            }
        }
    }

    public function loadAll($data)
    {
        $modelData = ArrayHelper::getValue($data, 'model');
        $saleData = ArrayHelper::getValue($data, 'model.sale');
        $positionsData = ArrayHelper::getValue($data, 'model.positions', []);
        $brandPositionsData = ArrayHelper::getValue($data, 'model.brandPositions', []);
        $documentsData = ArrayHelper::getValue($data, 'model.documents', []);

        $result = $this->load($modelData, '');
        $result = $this->sale->load($saleData, '') || $result;

        $this->positions = [];

        foreach ($positionsData as $positionData) {
            $position = new SalePosition();
            $position->load($positionData, '');
            $position->kg = intval(floatval($positionData['kg'] * 100));

            $this->positions[] = $position;
        }

        foreach ($brandPositionsData as $brandPositionData) {
            $position = new SaleBrandPosition();
            $position->load($brandPositionData, '');
            $position->kg = intval(floatval($brandPositionData['kg'] * 100));

            $this->positions[] = $position;
        }

        foreach ($documentsData as $documentData) {
            $document = SaleDocument::find()
                ->where(['id' => $documentData['id']])
                ->andWhere('sale_id IS NULL OR sale_id = :sale_id', [':sale_id' => $this->sale->isNewRecord ? 0 : $this->sale->id])
                ->one();

            if ($document === null) {
                continue;
            }

            if ($this->sale->isNewRecord) {
                $this->documents[] = $document;
            } else {
                $this->newDocuments[] = $document;
            }
        }

        return $result;
    }

	public function hasErrors($attribute = null)
	{
		if ($attribute !== null) {
			return parent::hasErrors($attribute);
		}

		return
			parent::hasErrors() ||
			array_reduce(array_merge([$this->sale], $this->positions), function ($hasErrors, Model $item) {
				return $hasErrors || $item->hasErrors();
			});
	}

	public function getFirstErrors()
	{
		return call_user_func_array('array_merge',
			array_merge([parent::getFirstErrors(), $this->sale->getFirstErrors()], array_map(function (Model $model) {
				return $model->getFirstErrors();
			}, $this->positions)));
	}

    public function process()
    {
        if (!$this->validateAll()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        if ($this->sale->isNewRecord === false) {
            SalePosition::deleteAll(['id' => $this->sale->getPositions()->select('id')->column()]);
            SaleBrandPosition::deleteAll(['id' => $this->sale->getBrandPositions()->select('id')->column()]);
            SaleDocument::updateAll(['sale_id' => null], ['sale_id' => $this->sale->id]);

            $documents = $this->newDocuments;
        }
        else {
            /** @var Dealer $dealer */
            $dealer = Yii::$app->request->get('dealer_id')
                ? Dealer::findOne(Yii::$app->request->get('dealer_id'))
                : Yii::$app->user->identity->profile->dealer;

            $this->sale->dealer_id = $this->dealer_id;
            $this->sale->promotion_id = $this->promotion_id;

            $documents = $this->documents;
        }

        $this->sale->status = $this->status;
        $this->sale->save(false);

        foreach ($this->positions as $position) {
            $position->sale_id = $this->sale->id;
            $position->save(false);
        }

        SaleDocument::updateAll([
            'sale_id' => $this->sale->id
        ], [
            'id' => ArrayHelper::getColumn($documents, 'id')
        ]);

        $this->sale->updateBonuses();
        $this->sale->updateKg();

        $transaction->commit();

        return true;
    }

    public function validateAll()
    {
        $result = true;
        $result = $this->validate() && $result;
        $result = $this->sale->validate() && $result;

        //		$result = array_reduce($this->positions, function ($result, SalePosition $position) {
        //			return $position->validate() && $result;
        //		}, $result);

        return $result;
    }
}