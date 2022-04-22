<?php

namespace modules\news\frontend\controllers;

use Yii;
use yii\web\Controller;
use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use modules\news\frontend\models\NewsSearch;

class NewsController extends Controller
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

    public function actionIndex()
    {
        /** @var NewsSearch $searchModel */
        $searchModel = Yii::createObject(NewsSearch::class);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->setSort([
            'defaultOrder' => [
                'created_at' => SORT_DESC
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}