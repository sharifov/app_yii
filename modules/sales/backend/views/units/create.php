<?php

use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Unit $model
 */
$this->title = \Yii::t('admin/t', 'Create {item}', ['item' => modules\sales\common\models\Unit::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => modules\sales\common\models\Unit::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="unit-create">

    <div class="text-right">
        <?php Box::begin() ?>
        <?= ActionButtons::widget([
            'order' => [['index', 'create', 'return']],
            'addReturnUrl' => false,
        ]) ?>
        <?php Box::end() ?>
    </div>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
