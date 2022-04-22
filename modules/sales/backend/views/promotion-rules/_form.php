<?php

use modules\sales\common\models\Promotion;
use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\PromotionRule $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'promotion-rule-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>

    <?= $form->field($model, 'promotion_id')->select2(Promotion::getOptions()) ?>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= $form->field($model, 'condition')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'rule')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'priority')->textInput() ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
