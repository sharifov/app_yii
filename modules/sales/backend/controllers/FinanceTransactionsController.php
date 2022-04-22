<?php

namespace modules\sales\backend\controllers;

use Yii;
use modules\sales\common\models\FinanceTransactions;
use modules\sales\backend\models\FinanceTransactionsSearch;
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

/**
 * FinanceTransactionsController implements the CRUD actions for FinanceTransactions model.
 */
class FinanceTransactionsController extends Controller implements AccessControlInterface
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
                    /** @var FinanceTransactionsSearch $searchModel */
                    return Yii::createObject(FinanceTransactionsSearch::className());
                },
                'dataProvider' => function($params, FinanceTransactionsSearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all FinanceTransactions models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var FinanceTransactionsSearch $searchModel */
        $searchModel = Yii::createObject(FinanceTransactionsSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(FinanceTransactionsSearch $searchModel)
    {
        return [

            [
                'attribute'=>'type',
                'label'=>'Операция',
                'filter'=>[
                    'in'=>'начисление',
                    'out'=>'списание',
                ],
                'value'=>function($data){
                    if($data->type == 'in')
                        return 'начисление';
                    else
                        return 'списание';

                }
            ],
            [
                'attribute'=>'adminName',
                'label' => 'ФИО Администратора'
            ],

            [
                'attribute'=>'profileTableName',
                'label'=> 'ФИО участника',
            ],
            [
                'attribute'=>'profileTablePhone',
                'label' => 'Номер телефона участника',
            ],

            [
                'attribute'=>'dealerTableName',
                'label' => 'Дилер',
            ],
            [
                'attribute'=>'promoTableName',
                'label' => 'Название акции',
            ],
            [
                'attribute' => 'comment',
                'label' => 'Комментарий',
            ],
			'amount',
			'created_at:datetime',
        ];
    }

    /**
     * Creates a new FinanceTransactions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FinanceTransactions;

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FinanceTransactions model.
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
     * Deletes an existing FinanceTransactions model.
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
     * Finds the FinanceTransactions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FinanceTransactions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FinanceTransactions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
