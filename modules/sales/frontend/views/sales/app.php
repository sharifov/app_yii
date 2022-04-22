<?php

/**
 * @var int | null $id
 * @var \modules\profiles\common\models\Dealer $dealer
 * @var Promotion $promotion
 */
use modules\sales\common\models\Promotion;
use yii\helpers\ArrayHelper;

$this->title = "Оформление продажи";
$this->params['breadcrumbs'][] = ['label' => "Дилер «{$dealer->name}» по акции «{$promotion->name}»", 'url' => ['/dashboard/index', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>

<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>

<div class="container">
    <h1><?= $this->title ?></h1>

    <div class="row">
        <div class="col-md-12">

            <?php if ($promotion->isGold()): ?>
                <?= \modules\sales\common\app\widgets\SaleGold::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'promotion_type' => $promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php elseif ($promotion->isProf()): ?>
                <?= \modules\sales\common\app\widgets\SaleProf::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'promotion_type' => $promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php elseif ($promotion->isApp2()): ?>
                <?= \modules\sales\common\app\widgets\SaleApp2::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'promotion_type' => $promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php else: ?>
                <?= \modules\sales\common\app\widgets\SaleApp::widget([
                    'id' => $id,
                    'dealer_id' => ArrayHelper::getValue($dealer, 'id'),
                    'promotion_id' => ArrayHelper::getValue($promotion, 'id'),
                    'promotion_type' => $promotion->type,
                    'config' => [
                        'apiUrlPrefix' => '/sales/sale-app',
                    ]
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
