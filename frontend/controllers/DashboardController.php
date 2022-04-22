<?php

namespace frontend\controllers;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Report;
use modules\sales\common\models\Promotion;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yz\Yz;

/**
 * Class DashboardController
 */
class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                    ]
                ]
            ]
        ];
    }

    public function actionIndex($dealer_id = null, $promotion_id = null)
    {
		$model = new Report();

		if ($uploadedFile = UploadedFile::getInstance($model, 'fileUpload')) {
			$model->scenario = Report::SCENARIO_FILE_UPLOAD;
			$model->fileUpload = $uploadedFile;
            $model->dealer_id = \Yii::$app->request->get('dealer_id');
			$model->profile_id = \Yii::$app->user->identity->profile->id;

			if ($model->save()) {
				\Yii::$app->getSession()->setFlash(Yz::FLASH_SUCCESS, 'Отчет об остатках был успешно загружен');
				return $this->redirect(['index']);
			}
		}
        $profileId = \modules\profiles\common\models\Profile::findOne(['identity_id'=> \Yii::$app->user->identity->id])->id;
        $person = \modules\profiles\common\models\Profile::isNdflRecord($profileId);
        $dealer = Dealer::findOne($dealer_id);
        $promotion = Promotion::findOne($promotion_id);

		return $this->render('index', compact('model', 'dealer', 'promotion', 'dealer_id', 'promotion_id' , 'person'));
    }

}