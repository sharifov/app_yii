<?php


namespace modules\profiles\backend\controllers;


use backend\base\Controller;
use modules\profiles\backend\models\DealerTransactionSearch;
use Yii;
use yii\db\Exception;

class DealerTransactionsController extends Controller
{
    /**
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new DealerTransactionSearch();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
        }

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $model->data(),
            'dataTotal' => $model->total()->getModels()[0]
        ]);
    }

    /**
     * @param null $dealer
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \yii\base\Exception
     */
    public function actionExport($dealer = null)
    {
        $model = new DealerTransactionSearch();
        $model->dealer = $dealer;

        if (($filePath = $model->excel()) !== null) {
            Yii::$app->response->sendFile($filePath);
        }
    }
}