<?php

namespace modules\sales\frontend\controllers;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\actions\DownloadDocumentAction;
use modules\sales\common\actions\DownloadPreviousDocumentAction;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use modules\sales\common\models\SaleDocument;
use modules\sales\common\models\SalePreviousDocument;
use modules\sales\frontend\base\Controller;
use modules\sales\frontend\models\SaleSearch;
use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * SalesController implements the CRUD actions for Sale model.
 */
class SalesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $profile = Yii::$app->user->identity->profile;

                            return $profile && $profile->isManager();
                        }
                    ],
                    [
                        'allow' => false,
                    ]
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'download-document' => [
                'class' => DownloadDocumentAction::className(),
            ],
            'download-previous-document' => [
                'class' => DownloadPreviousDocumentAction::className(),
            ]
        ];
    }

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Lists all Sale models.
     *
     * @return mixed
     */
    public function actionIndex($dealer_id = null, $promotion_id = null)
    {
        $searchModel = new SaleSearch([
            'dealer_id' => $dealer_id,
            'promotion_id' => $promotion_id,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => ['created_at'=>SORT_DESC]
            ]);

        $dealer = Dealer::findOne($dealer_id);
        $promotion = Promotion::findOne($promotion_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dealer' => $dealer,
            'promotion' => $promotion,
        ]);
    }

    /**
     * @param $dealer_id
     * @return Dealer
     * @throws NotFoundHttpException
     */
    private function findDealer($dealer_id)
    {
        $dealer = Dealer::findOne($dealer_id);
        if ($dealer === null) {
            throw new NotFoundHttpException();
        }

        return $dealer;
    }

    public function actionApp($dealer_id = null, $promotion_id = null)
    {
        $id = \Yii::$app->request->get('id');

        if ($id !== null) {
            $this->findModel($id);
        }

        $dealer = Dealer::findOne($dealer_id);
        $promotion = Promotion::findOne($promotion_id);

        return $this->render('app', compact('id', 'dealer', 'promotion'));
    }

    /**
     * Displays a single Sale model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $dealer_id = null, $promotion_id = null)
    {
        $model = $this->findModel($id);

        $dealer = Dealer::findOne($dealer_id);
        $promotion = Promotion::findOne($promotion_id);

        return $this->render('view', compact('model', 'dealer', 'promotion'));
    }

    /**
     * Deletes an existing Sale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $dealer_id, $promotion_id)
    {
        $model = $this->findModel($id);

        if ($model->statusManager->canBeDeleted()) {
            $model->delete();
        }

        return $this->redirect(['index', 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]);
    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function findPromotion($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
