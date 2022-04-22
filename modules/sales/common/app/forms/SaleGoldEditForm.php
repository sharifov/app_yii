<?php

namespace modules\sales\common\app\forms;

use Carbon\Carbon;
use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Sale;
use modules\sales\common\models\SaleDocument;
use modules\sales\common\models\SalePosition;
use modules\sales\common\models\SalePreviousDocument;
use modules\sales\common\models\SaleValidationRule;
use modules\sales\common\sales\statuses\Statuses;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class SaleGoldEditForm
 */
class SaleGoldEditForm extends Model
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
     * @var SaleDocument[]
     */
    public $documents;
    /**
     * @var SalePreviousDocument[]
     */
    public $previous_documents;
    /**
     * @var bool
     */
    public $useValidationRules = true;

    public $positions = [];

    public function __construct(Sale $sale, $config = [])
    {
        $this->sale = $sale;

        parent::__construct($config);
    }

    public function init()
    {
        if ($this->sale->isNewRecord) {
            $this->sale->sold_on = Carbon::today()->toDateString();
        }

        $this->documents = $this->sale->documents;
        $this->previous_documents = $this->sale->previous_documents;
    }

    public function fields()
    {
        return [
            'sale',
            'documents',
            'previous_documents',
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

        if (count($this->documents) == 0) {
            $this->addError('documents', 'Вы должны добавить хотя бы один подтверждающий документ за продажу 2015 года');
        }

		if (Yii::$app->getModule('sales')->documentsRequired) {
			if (count($this->previous_documents) == 0 && $this->sale->previous_kg) {
				$this->addError('documents', 'Вы должны добавить хотя бы один подтверждающий документ за продажу 2014 года или же не заполнять продажу за 2014 год');
			}
		}

		if (Yii::$app->getModule('sales')->useValidationRules) {
            /** @var SaleValidationRule[] $validationRules */
            $validationRules = SaleValidationRule::find()->all();

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
        $documentsData = ArrayHelper::getValue($data, 'model.documents', []);
        $previousDocumentsData = ArrayHelper::getValue($data, 'model.previous_documents', []);

        $result = $this->load($modelData, '');
        $result = $this->sale->load($saleData, '') || $result;

        $this->positions = [];

        $this->sale->kg = intval(floatval($saleData['kg']) * 100);
        $this->sale->previous_kg = intval(floatval($saleData['previous_kg']) * 100);

        foreach ($documentsData as $documentData) {
            $document = SaleDocument::find()
                ->where([
                    'id' => ArrayHelper::getValue($documentData, 'id'),
                ])
                ->andWhere('sale_id IS NULL OR sale_id = :sale_id', [':sale_id' => $this->sale->isNewRecord ? 0 : $this->sale->id])
                ->one();

            if ($document === null) {
                continue;
            }
            $this->documents[] = $document;
        }

        foreach ($previousDocumentsData as $documentData) {
            $document = SalePreviousDocument::find()
                ->where([
                    'id' => ArrayHelper::getValue($documentData, 'id'),
                ])
                ->andWhere('sale_id IS NULL OR sale_id = :sale_id', [':sale_id' => $this->sale->isNewRecord ? 0 : $this->sale->id])
                ->one();

            if ($document === null) {
                continue;
            }
            $this->previous_documents[] = $document;
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
        if ($this->validateAll() == false) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        if ($this->sale->isNewRecord == false) {
            SaleDocument::updateAll(['sale_id' => null], ['sale_id' => $this->sale->id]);
            SalePreviousDocument::updateAll(['sale_id' => null], ['sale_id' => $this->sale->id]);
        }
        else {
            /** @var Dealer $dealer */
			$dealer = Yii::$app->request->get('dealer_id')
				? Dealer::findOne(Yii::$app->request->get('dealer_id'))
				: Yii::$app->user->identity->profile->dealer;

            $this->sale->dealer_id = $dealer->id;
            $this->sale->promotion_id = $dealer->promotion_id;
        }

        $this->sale->status = $this->status;
        $this->sale->save(false);

        SaleDocument::updateAll([
            'sale_id' => $this->sale->id
        ], [
            'id' => ArrayHelper::getColumn($this->documents, 'id')
        ]);

        SalePreviousDocument::updateAll([
            'sale_id' => $this->sale->id
        ], [
            'id' => ArrayHelper::getColumn($this->previous_documents, 'id')
        ]);

        $transaction->commit();

        return true;
    }

    public function validateAll()
    {
        $result = true;
        $result = $this->validate() && $result;
        $result = $this->sale->validate() && $result;
        $result = array_reduce($this->positions, function ($result, SalePosition $position) {
            return $position->validate() && $result;
        }, $result);

        return $result;
    }
}