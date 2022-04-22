<?php


namespace modules\spent\backend\controllers;


use backend\base\Controller;
use modules\spent\backend\models\SpentOrder;
use PhpOffice\PhpSpreadsheet\Exception;
use Yii;
use yii\base\InvalidConfigException;

class SpentController extends Controller
{
    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $model = new SpentOrder();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
        }

        return $this->render('index', [
            'model' => $model,
            'transactions' => $model->transaction()
        ]);
    }

    /**
     * @param $year
     * @param $month
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionExport($year, $month)
    {
        $model = new SpentOrder();
        $model->year = $year;
        $model->month = $month;

        if (($filePath = $model->excel($model->year)) !== null) {
            Yii::$app->response->sendFile($filePath);
        }
    }
}