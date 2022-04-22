<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use ms\loyalty\finances\backend\forms\TransactionForm;
use ms\loyalty\finances\backend\models\TransactionSearch;
use ms\loyalty\finances\common\components\CompanyAccount;
use ms\loyalty\finances\common\finances\BackendUserPartner;
use ms\loyalty\finances\common\models\Transaction;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yz\admin\actions\ExportAction;
use yz\admin\grid\columns\DataColumn;
use yz\admin\grid\filters\DateRangeFilter;
use yz\Yz;

/**
 * TransactionsController implements the CRUD actions for Transaction model.
 */
class TransactionsController extends Controller
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
                'dataProvider' => function($params) {
                        $searchModel = new TransactionSearch;
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns(),
        ]);
    }

    public function getGridColumns()
    {
        return [
			'id',
            [
                'attribute' => 'type',
                'filter' => Transaction::getTypeValues(),
                'titles' => Transaction::getTypeValues(),
                'labels' => [
                    Transaction::INCOMING => DataColumn::LABEL_SUCCESS,
                    Transaction::OUTBOUND => DataColumn::LABEL_DANGER,
                ]
            ],
			[
                'attribute' => 'amount',
                'value' => function (Transaction $transaction) {
                    return str_replace('.', ',', ($transaction->amount / 100) . '');
                },
            ],
			// 'balance_after',
			// 'partner_type',
			// 'partner_id',
			 'title',
			// 'comment',
			 [
                 'attribute' => 'created_at',
                 'format' => 'datetime',
                 'filter' => DateRangeFilter::instance(),
             ],
            [
                'attribute' => 'updated_at',
                'label' => 'Дата',
                'value' => function ($model) {
                    return (new \DateTime($model->created_at))->format('d.m.Y');
                }
            ]
        ];
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TransactionForm();
        $model->partner_type = BackendUserPartner::className();
        $model->partner_id = Yii::$app->user->id;
        $purse = CompanyAccount::getPurse();

		if ($model->load(\Yii::$app->request->post()) && $purse->addTransaction($model)) {
			\Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Views an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
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


    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param array|int $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id) ?
            \Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array)$id;

        foreach ($id as $id_)
            $this->findModel($id_)->delete();

        \Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransactionForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var TransactionForm $model */
        if (($model = TransactionForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
