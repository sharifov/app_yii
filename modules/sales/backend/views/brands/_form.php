<?php

use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Brand $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php $box = FormBox::begin(['cssClass' => 'brand-form box-primary', 'title' => '']) ?>
<?php $form = ActiveForm::begin(); ?>

<?php $box->beginBody() ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'x_real')->input('text')->hint('Используется для подсчета баллов по Индивидуальной программе «Для персонала дилера»') ?>
<?= $form->field($model, 'xprof')->input('integer')->hint('Используется для подсчета баллов по Индивидуальной программе «Профессиональный подход» (руб/делится)') ?>

<?php $box->endBody() ?>

<?php $box->actions([
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
]) ?>
<?php ActiveForm::end(); ?>

<?php FormBox::end() ?>
