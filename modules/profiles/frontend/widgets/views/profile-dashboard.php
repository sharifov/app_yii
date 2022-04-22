<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \yii\web\User $user
 * @var \yii\web\IdentityInterface $identity
 * @var \modules\profiles\common\models\Profile $profile
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Ваши личные данные
                </div>
                <div class="panel-body">
                    <strong>Добро пожаловать, <?= Html::encode($profile->full_name) ?></strong>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Бонусный баланс
                </div>
                <div class="panel-body">
                    <a href="<?= Url::to(['/profiles/bonuses-transactions/index']) ?>"><?= $profile->purse->balance ?> баллов</a>
                </div>
            </div>
        </div>
    </div>
</div>