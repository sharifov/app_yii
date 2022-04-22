<?php

use ms\loyalty\finances\common\models\Transaction;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var marketingsolutions\finance\models\Transaction $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'transaction-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => 128]) ?>
<?= $form->field($model, 'type')->radioList(Transaction::getTypeValues()) ?>
<?= $form->field($model, 'amountRubles')->textInput() ?>
<?= $form->field($model, 'comment')->textInput(['maxlength' => 255]) ?>
    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
