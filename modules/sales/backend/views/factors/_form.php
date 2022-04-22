<?php

use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Brand;
use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Factor $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'factor-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>
    <?= $form->field($model, 'x_real')->input('integer') ?>

    <?= $form->field($model, 'dealer_id')->dropDownList(Dealer::getOptions()) ?>

    <?= $form->field($model, 'brand_id')->dropDownList(Brand::getOptions()) ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
