<?php

use modules\sales\backend\assets\FancyBoxAsset;
use modules\sales\common\sales\statuses\Statuses;
use marketingsolutions\thumbnails\Thumbnail;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Sale $model
 */
$this->title = \Yii::t('admin/t', 'Update {item}', ['item' => modules\sales\common\models\Sale::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => modules\sales\common\models\Sale::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

FancyBoxAsset::register($this);

$attributes = [
	'dealer.name',
	'promotion.name',
	'id',
	'created_at',
	[
		'attribute' => 'status',
		'value' => $model->getStatusValues()[$model->status],
	],
];

if ($model->promotion->isProf()) {
	$attributes[] = ['attribute' => 'kg_real', 'label' => 'На сумму в рублях'];
}
else {
	$attributes[] = 'kg_real';
}

if ($model->promotion->isGold()) {
	$attributes[] = 'previous_kg_real';
}
else {
	$attributes[] = 'sold_on:date';
}

$attributes[] = 'rub';
$attributes[] = 'bonuses';

if ($model->promotion->isProf() == false) {
	$attributes[] = 'dealer_bonuses';
	$attributes[] = 'manager_bonuses';
	$attributes[] = 'manager_commission';
	$attributes[] = 'manager_commission_included:boolean';
}
?>
	<div class="sale-update">

		<div class="text-right">
			<?php Box::begin() ?>
			<?= ActionButtons::widget([
				'order' => [['index', 'return']],
				'addReturnUrl' => false,
			]) ?>
			<?php Box::end() ?>
		</div>

		<div class="row">
			<div class="col-md-9">
				<?php $box = Box::begin(['cssClass' => 'sale-form box-primary', 'title' => '']) ?>
				<?= DetailView::widget(['model' => $model, 'attributes' => $attributes]); ?>

				<?php if (!empty($model->positions)): ?>
					<h3>Состав продажи: товарные позиции</h3>

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

				<?php if (!empty($model->brandPositions)): ?>
					<h3>Состав продажи: торговые марки</h3>


					<table class="table">
						<tr>
							<th>Бренд</th>
							<th><?= 'кг/руб' ?></th>
						</tr>
						<?php foreach ($model->brandPositions as $brandPosition): ?>
							<tr>
                                <?php if(isset($brandPosition->brand->name)):?>
								<td><?=  Html::encode($brandPosition->brand->name); ?></td>
								<td><?= empty($brandPosition->kg_real) ? $brandPosition->rub . ' руб.' : $brandPosition->kg_real . ' кг.' ?></td>
                                <?php endif;?>
							</tr>
						<?php endforeach ?>
					</table>
				<?php endif; ?>

				<div class="row">
					<?php if (count($model->documents)): ?>
						<div class="col-md-6">
							<h3>Подтверждающие документы</h3>

							<div class="row">
								<?php foreach ($model->documents as $document): ?>
									<div class="col-md-3">
										<a href="<?= Url::to(['download-document', 'id' => $document->id]) ?>"
										   rel="group"
										   target="_blank"
										   class="thumbnail <?= $document->isImage ? 'fancybox' : '' ?>">
                                            Прикрепленный документ
										</a>
									</div>
								<?php endforeach ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if (count($model->previous_documents)): ?>
						<div class="col-md-6">
							<h3>Подтверждающие документы за 2014г.</h3>

							<div class="row">
								<?php foreach ($model->previous_documents as $document): ?>
									<div class="col-md-3">
										<a href="<?= Url::to(['download-document', 'id' => $document->id]) ?>"
										   rel="group"
										   target="_blank"
										   class="thumbnail <?= $document->isImage ? 'fancybox' : '' ?>">
                                            Прикрепленный документ
										</a>
									</div>
								<?php endforeach ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<?php $box = Box::end() ?>
			</div>
			<div class="col-md-3">
				<?php Box::begin(['cssClass' => 'box-primary', 'title' => 'Статус продажи']) ?>

                <h3 style="margin:0 0 40px;"><?= $model->renderStatusButton() ?></h3>

				<?php if ($model->statusManager->adminCanEdit()): ?>
					<a class="btn btn-success btn-block"
					   href="<?= Url::to(['app', 'id' => $model->id]) ?>">
						<i class="fa fa-spin"></i> Изменить состав продажи
					</a>
				<?php endif ?>
				<?php if ($model->statusManager->adminCanSetStatus(Statuses::PAID) && \modules\sales\common\models\Sale::getRoleNameForAdmin()!='yar_admin'): ?>
					<a class="btn btn-success btn-block"
					   href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Statuses::PAID]) ?>">
						<i class="fa fa-dollar"></i> Начислить баллы
					</a>
				<?php endif; ?>

				<?php if ($model->statusManager->adminCanSetStatus(Statuses::APPROVED)): ?>
					<a class="btn btn-success btn-block"
					   href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Statuses::APPROVED]) ?>">
						<i class="fa fa-check"></i> Одобрить продажу
					</a>
				<?php endif ?>

				<?php if ($model->statusManager->adminCanSetStatus(Statuses::DECLINED)): ?>
					<a class="btn btn-danger btn-block"
					   href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Statuses::DECLINED]) ?>">
						<i class="fa fa-cross"></i> Отклонить продажу
					</a>
				<?php endif ?>

				<?php if ($model->statusManager->adminCanSetStatus(Statuses::DRAFT)): ?>
					<a class="btn btn-default btn-block"
					   href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Statuses::DRAFT]) ?>">
						<i class="fa fa-cross"></i> Перевести в статус "Черновик" и вернуть участнику
					</a>
				<?php endif ?>

				<?php if ($model->status == Statuses::PAID): ?>
					Продажа закрыта. Бонусы по продаже уже начислены
				<?php endif; ?>

				<?php Box::end() ?>
			</div>
		</div>

	</div>

<?php

$js = <<<JS
$('.fancybox').fancybox();
JS;
$this->registerJs($js);
