<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\backend\models\ActiveProfilesSearch;
use Yii;
use yz\admin\actions\ExportAction;

/**
 * ActiveProfilesController implements the CRUD actions for Report model.
 */
class ActiveProfilesController extends Controller
{
    protected $types = ['ms\loyalty\prizes\payments\common\finances\PaymentPartner', 'ms\loyalty\catalog\common\models\CatalogOrder'];
    protected $months = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];

    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::className(),
                'searchModel' => function ($params) {
                    /** @var ActiveProfilesSearch $searchModel */
                    return Yii::createObject(ActiveProfilesSearch::className());
                },
                'dataProvider' => function ($params, ActiveProfilesSearch $searchModel) {
                    $dataProvider = $searchModel->search($params);

                    return $dataProvider;
                },
            ],
        ]);
    }

    /**
     * Lists all Report models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var ActiveProfilesSearch $searchModel */
        $searchModel = Yii::createObject(ActiveProfilesSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(ActiveProfilesSearch $searchModel)
    {
        return [
            [
                'attribute' => 'monthYear',
                'label'     => 'Год и месяц',
                'format'    => 'html',
                'value'     => function ($model) {
                    $parts = explode(' ', $model['monthYear']);

                    if (isset($parts[0], $parts[1])) {
                        return $parts[1] . ' &ndash; ' . $this->months[$parts[0] - 1];
                    }

                    return '';
                }
            ],
            [
                'attribute' => 'users_count',
                'label'     => 'Число активных участников (вывели баллы)',
                'value'     => function ($model) {
                    return Transaction::find()->select(['purse_id'])
                        ->where(["DATE_FORMAT(created_at, '%m %Y')" => $model['monthYear']])
                        ->andWhere(['partner_type' => $this->types, 'type' => Transaction::OUTBOUND])
                        ->andWhere(['not', ['purse_id' => 4]])
                        ->groupBy('purse_id')
                        ->count();
                }
            ],
            [
                'attribute' => 'bonuses',
                'label'     => 'Сумма выведенных баллов',
            ],
        ];
    }
}
