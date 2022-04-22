<?php

use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yz\icons\Icons;

/* @var $this yii\web\View */
/* @var \modules\profiles\common\models\Dealer $dealer
/* @var Promotion $promotion
 * @var $searchModel modules\sales\frontend\models\SaleSearch
 */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Продажи";
if (!empty($dealer)) {
    $this->params['breadcrumbs'][] = ['label' => "Дилер «{$dealer->name}»", 'url' => ['/dashboard/index', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
}
$this->params['breadcrumbs'][] = $this->title;

\yz\icons\FontAwesomeAsset::register($this);

$columns = ['id',
    [
        'attribute' => 'status',
        'value' => function (Sale $data) {
            return Sale::getStatusValues()[$data->status];
        }
    ],
    'kg_real',
    'rub',
    'sold_on:date',
    'created_at:datetime',
    [
        'attribute' => 'bonuses',
        'value' => function (Sale $sale) {
            return $sale->isPaid() ? $sale->bonuses : '';
        }
    ],
    [
        'attribute' => 'dealer_bonuses',
        'value' => function (Sale $sale) {
            return $sale->isPaid() ? $sale->dealer_bonuses : '';
        }
    ],
    [
        'attribute' => 'manager_bonuses',
        'value' => function (Sale $sale) {
            return $sale->isPaid() ? $sale->manager_bonuses : '';
        }
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '<nobr>{view} {update} {delete}</nobr>',
        'buttons' => [
            'view' => function ($url, Sale $model, $key) {
                return Html::a(Icons::p('eye'), ['view', 'id' => $model->id, 'dealer_id' => $model->dealer_id, 'promotion_id' => $model->promotion_id], ['class' => 'btn btn-success']);
            },
            'update' => function ($url, Sale $model, $key) {
                if ($model->statusManager->recipientCanEdit()) {
                    return Html::a(Icons::p('pencil-square-o'), ['sales/app', 'id' => $model->id, 'dealer_id' => $model->dealer_id, 'promotion_id' => $model->promotion_id], ['class' => 'btn btn-success']);
                }
                else {
                    return '';
                }
            },
            'delete' => function ($url, Sale $model, $key) {
                if ($model->statusManager->canBeDeleted()) {
                    return Html::a(Icons::p('trash-o'), ['delete', 'id' => $model->id, 'dealer_id' => $model->dealer_id, 'promotion_id' => $model->promotion_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить заявку?',
                            'method' => 'post',
                        ]
                    ]);
                }
                else {
                    return '';
                }
            }
        ]
    ],
];
?>

<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <h1>Продажи</h1>
        </div>
        <div class="col-md-4" style="padding:20px 0 28px;">
            <a class="btn btn-success"
               href="<?= Url::to(['sales/app', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]) ?>">
                <i class="fa fa-plus"></i> Добавить продажу
            </a>
        </div>
    </div>
</div>

<div class="container">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>
</div>
