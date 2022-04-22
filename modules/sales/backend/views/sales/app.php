<?php

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Sale $model
 */
use modules\profiles\common\models\Profile;
use yii\helpers\ArrayHelper;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

$this->title = 'Редактирование продажи';
$this->params['breadcrumbs'][] = ['label' => modules\sales\common\models\Sale::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="sale-app">

	<div class="text-right">
		<?php Box::begin() ?>
		<?= ActionButtons::widget([
			'order' => [['index', 'return']],
			'addReturnUrl' => false,
		]) ?>
		<?php Box::end() ?>
	</div>

	<div class="row">
		<div class="col-lg-9">

			<?php $box = Box::begin(['title' => 'Редактирование продажи']) ?>

			<?php if ($model->promotion->isGold()): ?>
				<?= \modules\sales\common\app\widgets\SaleGold::widget([
					'id' => $model->id,
                    'dealer_id' =>  $model->dealer_id,
                    'promotion_id' => $model->promotion_id,
                    'promotion_type' => $model->promotion->type,
					'config' => [
						'apiUrlPrefix' => '/sales/sale-app'
					]
				]) ?>
			<?php elseif ($model->promotion->isIndividual()): ?>
				<?= \modules\sales\common\app\widgets\SaleApp::widget([
					'id' => $model->id,
                    'dealer_id' =>  $model->dealer_id,
                    'promotion_id' => $model->promotion_id,
                    'promotion_type' => $model->promotion->type,
					'config' => [
						'apiUrlPrefix' => '/sales/sale-app'
					]
				]) ?>
			<?php elseif ($model->promotion->isProf()): ?>
				<?= \modules\sales\common\app\widgets\SaleProf::widget([
					'id' => $model->id,
                    'dealer_id' =>  $model->dealer_id,
                    'promotion_id' => $model->promotion_id,
                    'promotion_type' => $model->promotion->type,
					'config' => [
						'apiUrlPrefix' => '/sales/sale-app',
					]
				]) ?>
            <?php elseif ($model->promotion->isApp2()): ?>
                <?= \modules\sales\common\app\widgets\SaleApp2::widget([
                    'id' => $model->id,
                    'dealer_id' =>  $model->dealer_id,
                    'promotion_id' => $model->promotion_id,
                    'promotion_type' => $model->promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php else: ?>
                <?= \modules\sales\common\app\widgets\SaleApp::widget([
                    'id' => $model->id,
                    'dealer_id' =>  $model->dealer_id,
                    'promotion_id' => $model->promotion_id,
                    'promotion_type' => $model->promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
			<?php endif; ?>

			<?php Box::end() ?>
		</div>
	</div>

</div>