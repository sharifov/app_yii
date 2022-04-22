<?php

use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Category $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php $box = FormBox::begin(['cssClass' => 'category-form box-primary', 'title' => '']) ?>
<?php $form = ActiveForm::begin(); ?>

<?php $box->beginBody() ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?php $box->endBody() ?>

<?php $box->actions([
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
]) ?>
<?php ActiveForm::end(); ?>

<?php FormBox::end() ?>
