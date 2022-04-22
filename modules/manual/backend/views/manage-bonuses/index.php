<?php

use ms\loyalty\contracts\profiles\HasEmailInterface;
use ms\loyalty\contracts\profiles\HasPhoneMobileInterface;
use ms\loyalty\contracts\profiles\UserNameInterface;
use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var \modules\manual\backend\forms\ChangeBalanceForm $model
 */
$this->title = 'Корректировка баланса участника';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="category-create">

    <?php $box = FormBox::begin(['cssClass' => 'box-primary', 'title' => 'Корректировка баланса участника']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>

    <div class="row">
        <div class="col-md-6 col-sm-offset-2">
            <table class="table">
                <tr>
                    <th>ID участика</th>
                    <td><?= Html::encode($model->profile->getProfileId()) ?></td>
                </tr>

                <?php if ($model->profile instanceof UserNameInterface): ?>
                    <tr>
                        <th>Участник</th>
                        <td><?= Html::encode($model->profile->getFullName()) ?></td>
                    </tr>
                <?php endif ?>

                <?php if ($model->profile instanceof HasPhoneMobileInterface): ?>
                    <tr>
                        <th>Номер телефона</th>
                        <td><?= Html::encode($model->profile->getPhoneMobile()) ?></td>
                    </tr>
                <?php endif ?>

                <?php if ($model->profile instanceof HasEmailInterface): ?>
                    <tr>
                        <th>Электронная почта</th>
                        <td><?= Html::encode($model->profile->getEmail()) ?></td>
                    </tr>
                <?php endif ?>

                <tr>
                    <th>Баланс счета</th>
                    <td><?= $model->profile->getRecipientPurse()->balance ?> баллов</td>
                </tr>

            </table>
        </div>
    </div>


    <?= $form->field($model, 'type')->radioList($model->getTypeValues()) ?>
    <?= $form->field($model, 'amount') ?>
    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'comment') ?>
    <?= $form->field($model, 'admin_id')->hiddenInput(['value' => \Yii::$app->user->identity->id])->label(false) ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        Html::submitButton('Изменить баланс', ['class' => 'btn btn-primary'])
    ]) ?>
    <?php ActiveForm::end(); ?>

    <?php FormBox::end() ?>

</div>
