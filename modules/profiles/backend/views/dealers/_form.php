<?php

use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Promotion;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\profiles\common\models\Dealer $model
 * @var yz\admin\widgets\ActiveForm $form
 */

?>

<div class="row">
    <div class="col-md-9">
        <?php $box = FormBox::begin(['cssClass' => 'dealer-form box-primary', 'title' => '']) ?>
        <?php $form = ActiveForm::begin(); ?>

        <?php $box->beginBody() ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'promotionIds')->select2(Promotion::getOptions(), [
            'multiple' => 'multiple',
        ]) ?>
        <?= $form->field($model, 'manager_commission')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'manager_commission_included')->checkbox() ?>
        <?= $form->field($model, 'admin_user_id')->select2(Dealer::getManagerOptions()) ?>
        <?php if(isset(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id)['yar_admin']) || Yii::$app->user->identity->id ==1):?>
        <?= $form->field($model, 'resolve_phone')->checkbox() ?>
        <?= $form->field($model, 'resolve_purse')->checkbox() ?>
        <?php endif;?>

        <?php $box->endBody() ?>

        <?php $box->actions([
            AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
            AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
            AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
        ]) ?>
        <?php ActiveForm::end(); ?>

        <?php FormBox::end() ?>
    </div>
</div>

