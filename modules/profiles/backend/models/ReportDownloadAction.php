<?php

namespace modules\profiles\backend\models;

use console\base\Controller;
use modules\profiles\common\models\Report;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * Class ReportDownloadAction
 */
class ReportDownloadAction extends Action
{
	/**
	 * @var callable|null Callback used to check if current user can have access to the requested certificate
	 */
	public $checkAccess;

	public function run($id)
	{
		/** @var Report $report */
		$report = Report::findOne($id);

		if ($report === null) {
			throw new NotFoundHttpException();
		}

		if ($this->checkAccess !== null) {
			if (call_user_func($this->checkAccess, $report) == false) {
				throw new NotFoundHttpException();
			}
		}

		$fileName = \Yii::getAlias("@data/sales/reports/{$report->name}");

		return \Yii::$app->response->sendFile($fileName, $report->name);
	}
}