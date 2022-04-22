<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\app\forms\SaleGoldEditForm;
use modules\sales\common\models\Sale;
use modules\sales\frontend\base\ApiController;
use yii\web\NotFoundHttpException;

/**
 * Class SalesGoldController
 *
 * @property SaleApplicationModule $module
 */
class SalesGoldController extends ApiController
{
	protected function verbs()
	{
		return [
			'post' => ['create', 'update']
		];
	}

	public function actionNew()
	{
		return new SaleGoldEditForm(new Sale());
	}

	public function actionView($id)
	{
		return new SaleGoldEditForm($this->findModel($id));
	}

	/**
	 * Creates a new Sale model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 * @throws NotFoundHttpException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate()
	{
		$model = new SaleGoldEditForm(new Sale(), ['useValidationRules' => $this->module->useValidationRules]);
		$model->loadAll(\Yii::$app->request->post());

		if ($model->process()) {
			return ['url' => call_user_func($this->module->afterSaleProcess, $model->sale)];
		}

		return $model;
	}

	/**
	 * Updates an existing Sale model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = new SaleGoldEditForm($this->findModel($id), ['useValidationRules' => $this->module->useValidationRules]);

		$model->loadAll(\Yii::$app->request->post());

		if ($model->process()) {
			return ['url' => call_user_func($this->module->afterSaleProcess, $model->sale)];
		}

		return $model;
	}

	/**
	 * Finds the Sale model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Sale the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if ($this->module->findSale !== null) {
			return call_user_func($this->module->findSale, $id);
		}

		/** @var Sale $model */
		if (($model = Sale::findOne($id)) === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		return $model;
	}
}