<?php

use yii\helpers\Html;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\sales\backend\models\FinanceTransactionsSearch $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<div class="finance-transactions-search hidden" id="filter-search">
    <?php $box = FormBox::begin() ?>
    <?php $box->beginBody() ?>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'fieldConfig' => [
            'horizontalCssClasses' => ['label' => 'col-sm-3', 'input' => 'col-sm-5', 'offset' => 'col-sm-offset-3 col-sm-5'],
        ],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'purse_id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'balance_before') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'balance_after') ?>

    <?php // echo $form->field($model, 'partner_type') ?>

    <?php // echo $form->field($model, 'partner_id') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'created_at') ?>

        <?php  $box->endBody() ?>
        <?php  $box->beginFooter() ?>
            <?= Html::submitButton(\Yii::t('admin/t','Search'), ['class' => 'btn btn-primary']) ?>
        <?php  $box->endFooter() ?>

    <?php ActiveForm::end(); ?>
    <?php  FormBox::end() ?>
</div>
