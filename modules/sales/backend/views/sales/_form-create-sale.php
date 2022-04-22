<?php
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;
use yz\admin\helpers\AdminHtml;
use yii\jui\DatePicker;
?>

<div class="row col-md-12">
    <?php $box = FormBox::begin(['cssClass' => 'create-sale-form box-primary', 'title' => '']) ?>
        <?php $form = ActiveForm::begin(); ?>
            <?php $box->beginBody() ?>
                <?= $form->field($model, 'status')->select2($model->getStatusOptions()) ?>

                <?= $form->field($model, 'sold_on_local')->widget(DatePicker::class, [
                    'language' => 'ru',
                    'options' => [
                        'class'=> 'form-control',
                        'autocomplete'=>'off'
                    ],
                ]) ?>
                <?= $form->field($model, 'kg')->textInput() ?>
                <?= $form->field($model, 'bonuses')->textInput() ?>
                <?= $form->field($model, 'rub')->textInput() ?>
                <?= $form->field($model, 'dealer_id')->select2($model->getDealers(), ['prompt' => 'не выбран']) ?>
                <?= $form->field($model, 'promotion_id')->select2($model->getPromotions(), ['prompt' => 'не выбран']) ?>
            <?php $box->endBody() ?>

            <?php $box->actions([
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
            ]) ?>
        <?php ActiveForm::end(); ?>
    <?php FormBox::end() ?>
</div>
