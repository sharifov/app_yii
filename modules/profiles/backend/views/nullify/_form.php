<?php

use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\profiles\common\models\Nullify $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'nullify-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>
    <?= $form->field($model, 'profile_id')->textInput() ?>

    <?= $form->field($model, 'sum')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
