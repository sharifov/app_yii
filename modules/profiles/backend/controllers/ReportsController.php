<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use modules\profiles\backend\models\ReportDownloadAction;
use modules\profiles\backend\models\ReportWithDataSearch;
use Yii;
use modules\profiles\common\models\Report;
use modules\profiles\backend\models\ReportSearch;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yz\admin\actions\ExportAction;
use yz\icons\Icons;
use yz\Yz;

/**
 * ReportsController implements the CRUD actions for Report model.
 */
class ReportsController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
                'searchModel' => function ($params) {
                    /** @var ReportSearch $searchModel */
                    return Yii::createObject(ReportSearch::className());
                },
                'dataProvider' => function ($params, ReportSearch $searchModel) {
                    $dataProvider = $searchModel->search($params);

                    return $dataProvider;
                },
            ],
            'download' => [
                'class' => ReportDownloadAction::className(),
            ]
        ]);
    }

    /**
     * Lists all Report models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var ReportSearch $searchModel */
        $searchModel = Yii::createObject(ReportWithDataSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(ReportSearch $searchModel)
    {
        return [
            'id',
            'name',
            'created_at:datetime',
            'dealer__name',
            'profile__full_name',
            [
                'attribute' => 'status',
                'value' => function (Report $data) {
                    return $data->renderStatusButton();
                },
                'format' => 'html',
            ],
            [
                'header' => 'Скачать',
                'format' => 'html',
                'value' => function (Report $data) {
                    return Html::a(Icons::i('download'), ['download', 'id' => $data->id], ['class' => 'btn btn-primary']);
                }
            ],
        ];
    }

    /**
     * Creates a new Report model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Report;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));

            return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionChangeStatus($id, $status)
    {
        $model = $this->findModel($id);

        if ($model->adminCan($status)) {
            $model->status = $status;
            $model->updateAttributes(['status']);

            \Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Статус отчета успешно изменен');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Deletes an existing Report model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id) ?
            \Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array) $id;

        foreach ($id as $id_) {
            $this->findModel($id_)->delete();
        }

        \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
