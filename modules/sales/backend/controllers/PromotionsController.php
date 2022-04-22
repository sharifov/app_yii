<?php

namespace modules\sales\backend\controllers;

use Yii;
use modules\sales\common\models\Promotion;
use modules\sales\backend\models\PromotionSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yz\admin\actions\ExportAction;
use backend\base\Controller;

/**
 * PromotionsController implements the CRUD actions for Promotion model.
 */
class PromotionsController extends Controller
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
                    /** @var PromotionSearch $searchModel */
                    return Yii::createObject(PromotionSearch::className());
                },
                'dataProvider' => function($params, PromotionSearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all Promotion models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var PromotionSearch $searchModel */
        $searchModel = Yii::createObject(PromotionSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(PromotionSearch $searchModel)
    {
        return [
			'id',
			'name',
			'brand_names',
            'type_label',
        ];
    }

    /**
     * Creates a new Promotion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promotion;

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model->refresh();
            if ($model->rulesFile = UploadedFile::getInstance($model, 'rulesFile')) {
                $model->upload();
            }

			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Promotion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model->refresh();
            if ($model->rulesFile = UploadedFile::getInstance($model, 'rulesFile')) {
                $model->upload();
            }

			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully updated'));
			return $this->getCreateUpdateResponse($model);
		}

        return $this->render('update', [
            'model' => $model,
        ]);
	}

    /**
     * Deletes an existing Promotion model.
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
     * Finds the Promotion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promotion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
