<?php

namespace modules\profiles\frontend\controllers;

use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use marketingsolutions\finance\models\Purse;
use Yii;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\frontend\models\BonusesTransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BonusesTransactionsController implements the CRUD actions for Transaction model.
 */
class BonusesTransactionsController extends Controller
{
    /**
     * @var PrizeRecipientInterface
     */
    private $prizeRecipient;

    public function __construct($id, $module, PrizeRecipientInterface $prizeRecipient, $config = [])
    {
        $this->prizeRecipient = $prizeRecipient;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BonusesTransactionSearch($this->prizeRecipient);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne(['id' => $id, 'purse_id' => $this->prizeRecipient->recipientPurse->id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
