<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

$box = Box::begin(['cssClass' => 'select-dealer box-primary']) ?>

	<section class="content-header">
		<h1>Выбор дилера</h1>
		<ul class="breadcrumb">
			<li><a href="/">Главная</a></li>
			<li><a href="<?= Url::to(['/sales/sales/index']) ?>">Продажи</a></li>
			<li class="active">Выбор дилера</li>
		</ul>
	</section>

	<div class="text-right">
		<?php Box::begin() ?>
		<?= ActionButtons::widget([
			'order'        => [['return']],
			'addReturnUrl' => false,
		]) ?>
		<?php Box::end() ?>
	</div>

	<div class="row">
		<div class="col-md-8">

			<?= GridView::widget([
				'id'           => 'sale-grid',
				'dataProvider' => $dataProvider,
				'filterModel'  => $searchModel,
				'columns'      => $columns,
			]); ?>
		</div>
	</div>

<?php Box::end() ?>