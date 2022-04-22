<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \modules\news\common\models\News $model
 */

?>

<div class="publication">

    <h3 class="publication__title">
        <?= Html::encode($model->title) ?>
    </h3>

    <div class="publication__date">
        <?= (new \DateTime($model->created_at))->format('d.m.Y') ?>
    </div>

    <div class="publication__content">
        <?= $model->content ?>
    </div>
</div>



