<?php
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \modules\profiles\frontend\models\RegistrationAndLoginForm $model
 */
$profile = $model->profile;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <h1>Регистрация</h1>

            <?php $form = \yii\bootstrap\ActiveForm::begin(['layout' => 'horizontal']) ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Данные для доступа в личный кабинет
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Номер телефона</label>

                        <div class="col-sm-6">
                            <p class="form-control-static">
                                <?= Html::encode($profile->phone_mobile_local) ?>
                            </p>
                        </div>
                    </div>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'passwordCompare')->passwordInput() ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Персональные данные
                </div>
                <div class="panel-body">
                    <?= $form->field($profile, 'last_name') ?>
                    <?= $form->field($profile, 'first_name') ?>
                    <?= $form->field($profile, 'middle_name') ?>
                    <?= $form->field($profile, 'email')->input('email') ?>
                    <?= $form->field($profile, 'position') ?>
                    <?= $form->field($profile, 'sales_point_name') ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Дополнительные условия
                </div>
                <div class="panel-body">
                    <?php foreach ($profile->promotions as $promotion): ?>
                        <?= $form->field($model, 'agreeWithTerms')->checkbox()
                            ->label('Согласен / согласна с ' . Html::a('правилами', "@frontendWeb/media/uploads/promotion_{$promotion->id}.pdf", ['target' => '_blank']) . ' программы') ?>
                    <?php endforeach ?>
                    <?= $form->field($model, 'allowPersonalDataProcessing')->checkbox() ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-sm-6 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-signin"></i> Зарегистрироваться и
                            подтвердить данные
                        </button>
                    </div>
                </div>
            </div>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>