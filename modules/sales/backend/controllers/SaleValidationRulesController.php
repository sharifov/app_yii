<?php

namespace modules\sales\backend\controllers;

use backend\base\Controller;
use modules\sales\backend\models\SaleValidationRuleSearch;
use modules\sales\common\models\SaleValidationRule;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yz\admin\actions\ExportAction;

/**
 * SaleValidationRulesController implements the CRUD actions for SaleValidationRule model.
 */
class SaleValidationRulesController extends Controller
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
                    /** @var SaleValidationRuleSearch $searchModel */
                    return Yii::createObject(SaleValidationRuleSearch::className());
                },
                'dataProvider' => function ($params, SaleValidationRuleSearch $searchModel) {
                    $dataProvider = $searchModel->search($params);
                    return $dataProvider;
                },
            ]
        ]);
    }

    /**
     * Lists all SaleValidationRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var SaleValidationRuleSearch $searchModel */
        $searchModel = Yii::createObject(SaleValidationRuleSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(SaleValidationRuleSearch $searchModel)
    {
        return [
            'id',
			'promotion.name',
            'is_enabled:boolean',
            'name',
            'rule',
            'error',
        ];
    }

    /**
     * Creates a new SaleValidationRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SaleValidationRule;

        $model->is_enabled = true;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
            return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SaleValidationRule model.
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
     * Deletes an existing SaleValidationRule model.
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
     * Finds the SaleValidationRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleValidationRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleValidationRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
