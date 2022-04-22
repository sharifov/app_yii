<?php


namespace modules\interim\backend\controllers;


use backend\base\Controller;
use common\components\NumberColumn;
use Exception;
use modules\interim\backend\models\Interim;
use Yii;
use yz\admin\actions\ExportAction;

class InterimController extends Controller
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::class,
                'dataProvider' => function () {
                    $model = new Interim();
                    $model->year = Yii::$app->request->get('year');
                    $model->month = Yii::$app->request->get('month');
                    $dataProvider = $model->purses();
                    return $dataProvider;
                },
            ]
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new Interim();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
        }

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $model->purses(),
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
                'attribute' => 'dealer_name',
                'header' => 'Дилер (Администратор)',
                'format' => 'raw',
                'value' => function (array $data) {
                    return $data['manager_name']
                        ? $data['dealer_name'] . ' (' . $data['manager_name'] . ')'
                        : '';
                }
            ],
            [
                'attribute' => 'profile_name',
                'header' => 'Участник',
                'format' => 'raw',
                'value' => function (array $data) {
                    return $data['profile_name'] ?? '';
                },
                'footer' => 'Всего:'
            ],
            [
                'attribute' => 'dealer_balance',
                'header' => 'Баланс',
                'format' => 'html',
                'class' => NumberColumn::class,
                'value' => function (array $data) {
                    return $data['manager_name']
                        ? '<strong>' . $data['dealer_balance'] ?? 0 . '</strong>'
                        : $data['profile_balance'] ?? 0;
                }
            ]
        ];
    }
}