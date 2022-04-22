<?php

use ms\loyalty\prizes\payments\backend\models\PaymentSearch;
use ms\loyalty\prizes\payments\common\models\Payment;
use ms\loyalty\prizes\payments\common\models\PaymentRequest;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\icons\Icons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var ms\loyalty\prizes\payments\backend\models\PaymentSearch $searchModel
 * @var array $columns
 */

$this->title = ms\loyalty\prizes\payments\common\models\Payment::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

# CSS
$css = <<<CSS
	.wrapper {
		overflow-x: scroll !important;
		width: 3200px;
	}
    .last_payment_request_is_baf {
        background:#ffcacf!important;
    }
    .btn-danger.btn-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-danger.btn-secondary:hover {
        background-color: #6c757d!important;
        color: #fff;
    }
    .row-in-check-payment-status .label-default {
        background: #6c757d;
        color: #fff;
    }
CSS;
$this->registerCss($css);
?>

<?php $box = Box::begin(['cssClass' => 'payment-index box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [['export','rollback','return']],
        'gridId' => 'payment-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'ms\loyalty\prizes\payments\common\models\Payment',
        'buttons' => [
            'rollback' => function () {
                return \yii\bootstrap\Button::widget([
                    'tagName' => 'a',
                    'label' => Icons::p('refresh') . 'Откатить выбранные',
                    'encodeLabel' => false,
                    'options' => [
                        'href' => yii\helpers\Url::to('/payments/payments/rollback-selected'),
                        'class' => 'btn btn-warning action-buttons selection',
                        'id' => 'action-button-add-sms',
                        'data' => [
                            'grid' => 'payment-grid',
                            'grid-bind' => 'selection',
                            'grid-param' => 'ids',
                            'confirm' => 'Вы уверены?',
                        ],
                    ],
                ]);
            },
        ],

    ]) ?>
</div>

<?= GridView::widget([
    'id' => 'payment-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model, $key, $index, $grid) use ($paymentConfig) {

        $isFailStr = Payment::checkLastResponseOnStopPhrases($paymentConfig->stop_phrases_last_payment_request, $model->last_response);

        if ($isFailStr && $model->status != Payment::STATUS_ROLLBACK && $model->status != Payment::STAUS_CHECK_PAYMENT && $model->status != Payment::STATUS_SUCCESS) {
            return ['class' => 'last_payment_request_is_baf'];
        } elseif ($model->status == Payment::STAUS_CHECK_PAYMENT) {
            return ['class' => 'row-in-check-payment-status'];
        }
    },
    'columns' => array_merge([
        ['class' => 'yii\grid\CheckboxColumn'],
    ], [
        [
            'class' => 'yz\admin\widgets\ActionColumn',
            'template' => '{view} {confirm} {rollback} {renew} {checkpayment} {cancel}',
            'buttons' => [
                'confirm' => function ($url, Payment $model) {
                    if ($model->status != Payment::STATUS_WAITING) {
                        return '';
                    }

                    return Html::a(Icons::i('check'), $url, [
                        'title' => 'Подтвердить',
                        'data-confirm' => 'Вы действительно хотите подтвердить платеж и отправить его на обработку?',
                        'data-method' => 'post',
                        'class' => 'btn btn-info btn-sm',
                        'data-pjax' => '0',
                    ]);
                },
                'rollback' => function ($url, Payment $model) {
                    if ( in_array($model->status, [Payment::STATUS_ROLLBACK, Payment::STATUS_SUCCESS]) ) {
                        return '';
                    }

                    return Html::a(Icons::i('refresh'), $url, [
                        'title' => 'Откатить',
                        'data-confirm' => 'Вы действительно хотите откатить платеж и вернуть баллы?',
                        'data-method' => 'post',
                        'class' => 'btn btn-warning btn-sm',
                        'data-pjax' => '0',
                    ]);
                },
                'renew' => function ($url, Payment $model) {
                    if ($model->status == Payment::STATUS_NEW || $model->status == Payment::STATUS_SUCCESS) {
                        return '';
                    }

                    return Html::a(Icons::i('undo'), $url, [
                        'title' => 'Вернуть на обработку',
                        'data-confirm' => 'Вы действительно хотите вернуть платеж на обработку?',
                        'data-method' => 'post',
                        'class' => 'btn btn-success btn-sm',
                        'data-pjax' => '0',
                    ]);
                },
                'cancel' => function ($url, Payment $model) {
                    if ($model->status == Payment::STATUS_PROCESSING) {
                        return Html::a(Icons::i('ban'), $url, [
                            'title' => 'Заблокировать в 1С',
                            'data-confirm' => 'Вы действительно хотите заблокировать платеж в 1С?',
                            'data-method' => 'post',
                            'class' => 'btn btn-danger btn-sm',
                            'data-pjax' => '0',
                        ]);
                    }
                    else {
                        return '';
                    }
                },
                'checkpayment' => function ($url, Payment $model) use ($paymentConfig) {

                    $isFailStr = Payment::checkLastResponseOnStopPhrases($paymentConfig->stop_phrases_last_payment_request, $model->last_response);

                    if ($isFailStr && $model->status != Payment::STATUS_ROLLBACK && $model->status != Payment::STAUS_CHECK_PAYMENT && $model->status != Payment::STATUS_SUCCESS) {
                        return Html::a(Icons::i('exclamation-circle'), $url, [
                            'title' => 'Отправить на модерацию',
                            'data-confirm' => 'Вы действительно хотите отправить платеж на модерацию?',
                            'data-method' => 'post',
                            'class' => 'btn btn-danger btn-secondary btn-sm',
                            'data-pjax' => '0',
                        ]);
                    } else {
                        return '';
                    }
                }
            ],
        ]
    ], $columns),
]); ?>
<?php Box::end() ?>
