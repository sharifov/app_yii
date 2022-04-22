<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 */
?>

<?= \yii\widgets\Menu::widget([
    'options' => [
        'class' => 'nav navbar-nav navbar-right'
    ],
    'items' => [
        ['label' => 'Личный кабинет', 'url' => ['/dashboard/index'], 'visible' => ! Yii::$app->user->isGuest],
    ]
]) ?>