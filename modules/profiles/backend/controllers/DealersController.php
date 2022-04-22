<?php

namespace modules\profiles\backend\controllers;

use modules\sales\common\models\Factor;
use Yii;
use modules\profiles\common\models\Dealer;
use modules\profiles\backend\models\DealerSearch;
use backend\base\Controller;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yz\admin\actions\ExportAction;
use yz\admin\widgets\ActiveForm;
use yz\admin\traits\CheckAccessTrait;

/**
 * DealersController implements the CRUD actions for Dealer model.
 */
class DealersController extends Controller
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
                    /** @var DealerSearch $searchModel */
                    return Yii::createObject(DealerSearch::className());
                },
                'dataProvider' => function ($params, DealerSearch $searchModel) {
                    $dataProvider = $searchModel->search($params);

                    return $dataProvider;
                },
            ]
        ]);
    }

    /**
     * Lists all Dealer models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var DealerSearch $searchModel */
        $searchModel = Yii::createObject(DealerSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(DealerSearch $searchModel)
    {
        return [
            'id',
            'name',
            [
                'label' => 'Акции',
                'value' => function (Dealer $dealer) {
                    return implode(', ', ArrayHelper::getColumn($dealer->promotions, 'name'));
                }
            ],
            'x_real',
            'xx_real',
            ['attribute' => 'manager.full_name', 'label' => 'Руководитель'],
            'manager_commission',
            'manager_commission_included:boolean',
            [
                'label' => 'Коэффициенты по брендам',
                'format' => 'html',
                'value' => function (Dealer $model) {
                    return Html::ul($model->factors, ['item' => function (Factor $factor, $index) {
                        return Html::tag(
                            'li',
                            $factor->brand->name . ': ' . $factor->x_real,
                            ['class' => 'factor']
                        );
                    }]);
                }
            ],
            ['attribute' => 'adminUser.name', 'label' => 'Территориальный управляющий'],
        ];
    }

    /**
     * Creates a new Dealer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dealer;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));

            return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Dealer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully updated'));
            Dealer::updateProfilesId($id);
            return $this->getCreateUpdateResponse($model);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Dealer model.
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
     * Finds the Dealer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Dealer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dealer::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
