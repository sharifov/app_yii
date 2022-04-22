<?php


namespace modules\profiles\backend\controllers;


use backend\base\Controller;
use marketingsolutions\finance\models\Purse;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use yii\data\ActiveDataProvider;

class PursesController extends Controller
{
    public function actionIndex()
    {
        $query = Purse::find()
            ->where(['owner_type' => [Dealer::class, Profile::class]])
            ->orderBy(['owner_id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->pagination->pageSize = 0;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'columns' => $this->getGridColumns()
        ]);
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [
            [
                'attribute' => 'owner_id',
                'format' => 'raw',
                'header' => 'Владелец',
                'value' => function (Purse $model) {
                    $name = '';
                    switch ($model->owner_type) {
                        case Dealer::class:
                            $name = $model->title ?? $model->owner->name . ' (Дилер)';
                            break;
                        case Profile::class:
                            $name = $model->title ?? $model->owner->full_name . ' (Участник)';
                            break;
                    }
                    return $name;
                }
            ],
            [
                'attribute' => 'balance',
                'header' => 'Баланс',
                'format' => 'html',
                'value' => function (Purse $model) {
                    return $model->balanceByDate('01.01.2019');
                }
            ]
        ];
    }
}