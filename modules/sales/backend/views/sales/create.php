<?php

use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Promotion;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/* @var Promotion $promotion */
/* @var Dealer $dealer */

\modules\sales\common\app\assets\SaleAppAsset::register($this);

?>

<section class="content-header">
	<h1>Добавление продажи</h1>
	<ul class="breadcrumb">
		<li><a href="/">Главная</a></li>
		<li><a href="<?= Url::to(['/sales/sales/index']) ?>">Продажи</a></li>
		<li class="active">Добавление</li>
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

<div class="container">
	<div class="row">
		<div class="col-md-12">
            <?php if ($promotion->isGold()): ?>
                <?= \modules\sales\common\app\widgets\SaleGold::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php elseif ($promotion->isProf()): ?>
                <?= \modules\sales\common\app\widgets\SaleProf::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php else: ?>
                <?= \modules\sales\common\app\widgets\SaleApp::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php endif; ?>

		</div>
	</div>
</div>
