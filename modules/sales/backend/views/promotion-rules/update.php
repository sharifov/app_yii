<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\PromotionRule $model
 */
$this->title = \Yii::t('admin/t', 'Update {item}', ['item' => modules\sales\common\models\PromotionRule::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => modules\sales\common\models\PromotionRule::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="promotion-rule-update">

    <div class="text-right">
        <?php  Box::begin() ?>
        <?=  ActionButtons::widget([
            'order' => [['index', 'update', 'return']],
            'addReturnUrl' => false,
        ]) ?>
        <?php  Box::end() ?>
    </div>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
