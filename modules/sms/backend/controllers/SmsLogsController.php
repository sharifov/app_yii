<?php

namespace modules\sms\backend\controllers;

use Yii;
use modules\sms\common\models\SmsLog;
use modules\sms\backend\models\SmsLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yz\admin\actions\ExportAction;
use yz\admin\widgets\ActiveForm;
use yz\admin\traits\CheckAccessTrait;
use yz\admin\traits\CrudTrait;
use yz\admin\contracts\AccessControlInterface;
use yz\admin\grid\filters\DateRangeFilter;


/**
 * SmsLogsController implements the CRUD actions for SmsLog model.
 */
class SmsLogsController extends Controller implements AccessControlInterface
{
    use CrudTrait, CheckAccessTrait;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'accessControl' => $this->accessControlBehavior(),
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::className(),
                'searchModel' => function($params) {
                    /** @var SmsLogSearch $searchModel */
                    return Yii::createObject(SmsLogSearch::className());
                },
                'dataProvider' => function($params, SmsLogSearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all SmsLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var SmsLogSearch $searchModel */
        $searchModel = Yii::createObject(SmsLogSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		$dataProvider->setSort([
			'defaultOrder' => ['created_at' => SORT_DESC],
		]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(SmsLogSearch $searchModel)
    {
        return [
			'id',
//			'sms_id',
            [
                'attribute' => 'type',
                'titles' => SmsLog::getTypes(),
                'filter' => SmsLog::getTypes(),
            ],
			'service',
			'phone_mobile',
			'message:ntext',
			'status:boolean',
			[
                'attribute' => 'created_at',
                'filter' => DateRangeFilter::instance(),
            ],
//			'updated_at:datetime',
        ];
    }

    /**
     * Creates a new SmsLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SmsLog;

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SmsLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully updated'));
			return $this->getCreateUpdateResponse($model);
		}

        return $this->render('update', [
            'model' => $model,
        ]);
	}


    /**
     * Deletes an existing SmsLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id) ?
            \Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array)$id;

        foreach ($id as $id_)
            $this->findModel($id_)->delete();

        \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SmsLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SmsLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SmsLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
