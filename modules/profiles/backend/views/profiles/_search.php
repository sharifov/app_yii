<?php

use yii\helpers\Html;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\profiles\backend\models\ProfileSearch $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<div class="profile-search hidden" id="filter-search">
    <?php $box = FormBox::begin() ?>
    <?php $box->beginBody() ?>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'fieldConfig' => [
            'horizontalCssClasses' => ['label' => 'col-sm-3', 'input' => 'col-sm-5', 'offset' => 'col-sm-offset-3 col-sm-5'],
        ],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'first_name') ?>
    <?= $form->field($model, 'last_name') ?>
    <?= $form->field($model, 'middle_name') ?>
    <?php /* echo $form->field($model, 'full_name') */ ?>
    <?php /* echo $form->field($model, 'phone_mobile') */ ?>
    <?php /* echo $form->field($model, 'email') */ ?>
    <?php /* echo $form->field($model, 'created_at') */ ?>
    <?php /* echo $form->field($model, 'updated_at') */ ?>
        <?php  $box->endBody() ?>
        <?php  $box->beginFooter() ?>
            <?= Html::submitButton(\Yii::t('admin/t','Search'), ['class' => 'btn btn-primary']) ?>
        <?php  $box->endFooter() ?>

    <?php ActiveForm::end(); ?>
    <?php  FormBox::end() ?>
</div>
