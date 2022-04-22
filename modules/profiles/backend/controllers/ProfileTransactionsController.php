<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use modules\profiles\backend\models\ProfileTransactionExtra;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\common\models\Dealer;
use Yii;
use yii\base\InvalidConfigException;
use yz\admin\actions\ExportAction;
use yz\admin\grid\columns\DataColumn;


/**
 * Class ProfileTransactionsController
 */
class ProfileTransactionsController extends Controller
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::class,
                'dataProvider' => function ($params) {
                    $searchModel = Yii::createObject(ProfileTransactionExtra::class);
                    return $searchModel->search($params);
                },
            ]
        ]);
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        /**
         * @var ProfileTransactionExtra $searchModel
         */
        $searchModel = Yii::createObject(ProfileTransactionExtra::class);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns(),
        ]);
    }

    /**
     * @return array
     */
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
                ],
            ],
            [
                'attribute' => 'dealerNames',
                'value' => function (ProfileTransactionExtra $model) {
                    return $model->dealerNames ?? '';
                },
                'filter' => Dealer::getOptions('name')
            ],
            'profile__full_name',
            'profile__phone_mobile',
            'amount',
            'balance_before',
            'balance_after',
            'title',
            'comment',
            'created_at:datetime',
        ];
    }
}