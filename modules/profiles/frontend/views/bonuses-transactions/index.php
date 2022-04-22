<?php

use marketingsolutions\finance\models\Transaction;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\profiles\frontend\models\BonusesTransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'История бонусных баллов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="transaction-index">

                <h1><?= Html::encode($this->title) ?></h1>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
//                    'filterModel' => $searchModel,
                    'columns' => [
//                        'id',
//                        'purse_id',
                        [
                            'attribute' => 'type',
                            'format' => 'raw',
                            'value' => function (Transaction $data) {
                                if ($data->type == Transaction::INCOMING) {
                                    return Html::tag('span', 'Входящая', ['class' => 'label label-success']);
                                }

                                return Html::tag('span', 'Исходящая', ['class' => 'label label-danger']);
                            }
                        ],
                        'created_at:datetime',
//                        'balance_before',
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'value' => function (Transaction $data) {
                                return Yii::$app->formatter->asDecimal($data->amount, 0);
                            }
                        ],
                        // 'balance_after',
                        // 'partner_type',
                        // 'partner_id',
                         'title',
                         'comment',
//                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>

            </div>
        </div>
    </div>
</div>
