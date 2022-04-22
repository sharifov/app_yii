<?php

use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\FinanceTransactions $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'finance-transactions-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>
    <?= $form->field($model, 'purse_id')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'in' => 'In', 'out' => 'Out', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'balance_before')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'balance_after')->textInput() ?>

    <?= $form->field($model, 'partner_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'partner_id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
