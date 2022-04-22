<?php

use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var \ms\loyalty\finances\backend\forms\TransactionForm $model
 */
$this->title = \Yii::t('admin/t', 'Create {item}', ['item' => marketingsolutions\finance\models\Transaction::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => marketingsolutions\finance\models\Transaction::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="transaction-create">

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
