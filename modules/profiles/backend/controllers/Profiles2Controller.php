<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use modules\profiles\backend\models\ProfileSearchExtended;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use modules\profiles\backend\models\DateRangeConfig;
use marketingsolutions\widgets\DateRangePicker;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yz\admin\actions\ExportAction;
use yz\admin\grid\filters\Select2Filter;

/**
 * Profiles2Controller implements the CRUD actions for Profile model.
 */
class Profiles2Controller extends Controller
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
                'dataProvider' => function ($params) {
                    $searchModel = Yii::createObject(ProfileSearchExtended::class);
                    $dataProvider = $searchModel->search($params);

                    return $dataProvider;
                },
            ]
        ]);
    }

    /**
     * Lists all Profile models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProfileSearchExtended();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns($searchModel)
    {
        $columns = [
            'id',
            'phone_mobile',
            'full_name',
            'email:email',
            [
                'attribute' => 'sales_point_name',
                'format' => 'raw',
                'value' => function (Profile $profile) {
                    return $profile->sales_point_name;
                }
            ],
            'position',
            [
                'attribute' => 'promotions',
                'label' => 'Акции участника',
                'format' => 'raw',
                'filter' => Select2Filter::instance([
                    'attribute' => 'promotion_id',
                    'values' => Promotion::getOptions(),
                ]),
                'value' => function (Profile $profile) {
                    $text = '';
                    if ($profile->sync_with_dealers_promotions) {
                        $text .= Html::tag('span', 'Синхронизорованы с дилерами', ['class' => 'label label-info']).'<br>';
                    }
                    return $text . implode(', ', ArrayHelper::getColumn($profile->promotions, 'name'));
                }
            ],
            [
                'attribute' => 'dealers',
                'label' => 'Дилеры',
                'filter' => Select2Filter::instance([
                    'attribute' => 'dealer_id',
                    'values' => Dealer::getOptions(),
                ]),
                'value' => function (Profile $profile) {
                    return implode(', ', ArrayHelper::getColumn($profile->dealers, 'name'));
                }
            ],
            'purse__balance',
            [
                'attribute' => 'role',
                'titles' => Profile::getRoleValues(),
                'filter' => Profile::getRoleValues(),
                'contentOptions' => ['style' => 'width:160px'],
            ],
            [
                'attribute' => 'status',
                'titles' => Profile::getStatusValues(),
                'filter' => Profile::getStatusValues(),
                'contentOptions' => ['style' => 'width:160px'],
            ],
            'status_date:date',
            ['attribute' => 'bonuses_in', 'label' => 'Начисления'],
            ['attribute' => 'bonuses_eps', 'label' => 'Списания ЭПС'],
            ['attribute' => 'bonuses_yandex', 'label' => 'Списания Яндекс.Деньги'],
            ['attribute' => 'bonuses_mobile', 'label' => 'Списания на Мобильный'],
            ['attribute' => 'bonuses_ozon', 'label' => 'Списания на Озон'],
            // 'created_at:datetime',
            // 'updated_at:datetime',
        ];

        if (\Yii::$app->request->getPathInfo() == 'profiles/profiles2/export') {
            $columns = array_merge([
                [
                    'attribute' => 'dr',
                    'format' => 'raw',
                    'label' => 'Дата',
                    'value' => function($model) {
                        return empty($_GET['ProfileSearchExtended']) || empty($_GET['ProfileSearchExtended']['dr'])
                            ? null
                            : $_GET['ProfileSearchExtended']['dr'];
                    }
                ],
            ], $columns);
        }
        else {
            $columns = array_merge([
                [
                    'attribute' => 'dr',
                    'format' => 'date',
                    'label' => 'Дата',
                    'contentOptions' => ['style' => 'width:200px'],
                    'filter' => DateRangePicker::widget(DateRangeConfig::get($searchModel, 'dr', [
                        'model' => $searchModel,
                        'attribute' => 'dr',
                        'options' => [
                            'class' => 'form-control input-sm',
                        ]
                    ]))
                ],
            ], $columns);
        }

        return $columns;
    }

    /**
     * Creates a new Profile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Profile;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));

            return $this->getCreateUpdateResponse($model);
        }
        else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Profile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully updated'));

            return $this->getCreateUpdateResponse($model);
        }
        else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Обнуление кошельков участников
     */
    public function actionNullify()
    {
        \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, 'Баллы участников списаны');

        \Yii::$app->db
            ->createCommand('UPDATE {{%finance_purses}} SET balance = 0 WHERE owner_id IS NOT NULL')
            ->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing Profile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id) ?
            \Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array) $id;

        foreach ($id as $id_) {
            $this->findModel($id_)->delete();
        }

        \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }
}
