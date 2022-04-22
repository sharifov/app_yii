<?php

use modules\sales\common\models\Sale;
use marketingsolutions\thumbnails\Thumbnail;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\sales\common\models\Sale */

$dealer = $model->dealer;
$promotion = $model->promotion;

$this->title = 'Информация о продаже';
$this->params['breadcrumbs'][] = ['label' => "Дилер «{$dealer->name}» по акции «{$promotion->name}»", 'url' => ['/dashboard/index', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
$this->params['breadcrumbs'][] = ['label' => 'Продажи', 'url' => ['index', 'dealer_id' => $model->dealer_id, 'promotion_id' => $model->promotion_id]];
$this->params['breadcrumbs'][] = $this->title;

$viewAttr = [
    'id',
    [
        'attribute' => 'status',
        'value' => Sale::getStatusValues()[$model->status],
    ],
    'dealer.name',
    'promotion.name',
    'created_at:datetime',
    'sold_on:date',
];

if ($model->promotion->isProf()) {
	$viewAttr[] = ['attribute' => 'kg_real', 'label' => 'На сумму в рублях'];
}
else {
	$viewAttr[] = 'kg_real';
}
$viewAttr[] = 'rub_real';

if ($model->previous_kg) {
    $viewAttr[] = 'previous_kg_real';
}

if ($model->isPaid()) {
    $viewAttr[] = 'bonuses';
    $viewAttr[] = 'dealer_bonuses';
    $viewAttr[] = 'manager_bonuses';
}
?>

<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-2">
            <p>
                <?php if ($model->statusManager->recipientCanEdit()): ?>
                    <a class="btn btn-success" href="<?= Url::to(['sales/app', 'id' => $model->id]) ?>"><i
                            class="fa fa-pencil-o"></i> Изменить</a>
                <?php endif ?>
                <?php if ($model->statusManager->canBeDeleted()): ?>
                    <a class="btn btn-success" href="<?= Url::to(['delete']) ?>"><i class="fa fa-trash"></i> Удалить</a>
                <?php endif ?>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $viewAttr,
            ]) ?>

            <?php if (count($model->positions)): ?>
                <h3>Состав продажи</h3>

                <table class="table">
                    <tr>
                        <th>Тип</th>
                        <th>Вид</th>
                        <th>Бренд</th>
                        <th>Товар</th>
                        <th><?= $model->promotion->isProf() ? 'руб' : 'кг' ?></th>
                    </tr>
                    <?php foreach ($model->positions as $position): ?>
                        <tr>
                            <td><?= Html::encode($position->product->type->name) ?></td>
                            <td><?= Html::encode($position->product->category->name) ?></td>
                            <td><?= Html::encode($position->product->brand->name) ?></td>
                            <td><?= Html::encode($position->product->name) ?></td>
                            <td><?= $position->kg_real ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif; ?>

            <?php if (count($model->documents)): ?>
            <h3>Подтверждающие документы продажи</h3>

            <div class="row">
                <?php foreach ($model->documents as $document): ?>
                    <div class="col-md-3">
                        <a href="<?= Url::to(['download-document', 'id' => $document->id]) ?>" class="thumbnail">
                            <?php if ($document->isImage): ?>
                                <img
                                    src="<?= (new Thumbnail())->url($document->getFileName(), Thumbnail::thumb(300, 600), '300x600') ?>"
                                    alt=""/>
                            <?php else: ?>
                                <?= Html::encode($document->original_name) ?>
                            <?php endif ?>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
            <?php endif; ?>

            <?php if (count($model->previous_documents)): ?>
                <h3>Подтверждающие документы продажи за 2014 год</h3>

                <div class="row">
                    <?php foreach ($model->previous_documents as $document): ?>
                        <div class="col-md-3">
                            <a href="<?= Url::to(['download-document', 'id' => $document->id]) ?>" class="thumbnail">
                                <?php if ($document->isImage): ?>
                                    <img
                                        src="<?= (new Thumbnail())->url($document->getFileName(), Thumbnail::thumb(300, 600), '300x600') ?>"
                                        alt=""/>
                                <?php else: ?>
                                    <?= Html::encode($document->original_name) ?>
                                <?php endif ?>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="sale-view">


</div>
