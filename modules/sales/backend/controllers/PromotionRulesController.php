<?php

namespace modules\sales\backend\controllers;

use Yii;
use modules\sales\common\models\PromotionRule;
use modules\sales\backend\models\PromotionRuleSearch;
use backend\base\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yz\admin\actions\ExportAction;

/**
 * PromotionRulesController implements the CRUD actions for PromotionRule model.
 */
class PromotionRulesController extends Controller
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
                'searchModel' => function($params) {
                    /** @var PromotionRuleSearch $searchModel */
                    return Yii::createObject(PromotionRuleSearch::className());
                },
                'dataProvider' => function($params, PromotionRuleSearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all PromotionRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var PromotionRuleSearch $searchModel */
        $searchModel = Yii::createObject(PromotionRuleSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(PromotionRuleSearch $searchModel)
    {
        return [
			'id',
			'promotion.name',
            'description',
			'condition',
			'rule',
            'priority',
        ];
    }

    /**
     * Creates a new PromotionRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PromotionRule;

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PromotionRule model.
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
     * Deletes an existing PromotionRule model.
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
     * Finds the PromotionRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PromotionRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PromotionRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
