<?php

use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;
use modules\sms\common\models\SmsLog;

/**
 * @var yii\web\View $this
 * @var modules\sms\common\models\SmsLog $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'sms-log-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>
    <?php // $form->field($model, 'sms_id')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList(SmsLog::getTypes()) ?>

    <?= $form->field($model, 'service')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone_mobile')
			->textInput(['maxlength' => true])
			->hint('Номер телефона в формате +79141234567')
	?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(SmsLog::getStatuses()) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
