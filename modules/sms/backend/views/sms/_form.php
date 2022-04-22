<?php

use modules\sms\common\models\Sms;
use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sms\common\models\Sms $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'sms-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>

    <?= $form->field($model, 'status')->dropDownList(Sms::getStatusOptions(), ['disabled' => true]) ?>

	<?php if ($model->status == Sms::STATUS_DONE || $model->status == Sms::STATUS_PROGRESS): ?>
		<?= $form->field($model, 'type')->dropDownList(Sms::getTypeOptions(), ['disabled' => true]) ?>
		<?= $form->field($model, 'to')->textInput(['maxlength' => true, 'disabled' => true])->hint('Перечислите номера телефонов через ; в формате +79141234567') ?>
		<?= $form->field($model, 'message')->textarea(['disabled' => true]) ?>
	<?php else: ?>
		<?= $form->field($model, 'type')->dropDownList(Sms::getTypeOptions()) ?>
		<?= $form->field($model, 'to')->textInput(['maxlength' => true])->hint('Перечислите номера телефонов через ; в формате +79141234567') ?>
		<?= $form->field($model, 'message')->textarea() ?>
	<?php endif; ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
