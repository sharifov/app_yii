<?php

use modules\sales\common\models\Promotion;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\SaleValidationRule $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php $box = FormBox::begin(['cssClass' => 'sale-validation-rule-form box-primary', 'title' => '']) ?>
<?php $form = ActiveForm::begin(); ?>

<?php $box->beginBody() ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'rule')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'error')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'promotion_id')->select2(Promotion::getOptions()) ?>

<?= $form->field($model, 'is_enabled')->checkbox() ?>

<?php $box->endBody() ?>

<?php $box->actions([
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
]) ?>
<?php ActiveForm::end(); ?>

<?php FormBox::end() ?>

<?php $box = Box::begin(['cssClass' => 'box-info', 'title' => 'Подсказка по вводу правил']) ?>

<?= $this->render('partials/_formula-readme') ?>

<?php Box::end() ?>



