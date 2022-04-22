<?php

use modules\profiles\common\models\Report;
use modules\sales\common\sales\statuses\Statuses;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;
use yz\icons\Icons;

/**
 * @var yii\web\View $this
 * @var modules\profiles\common\models\Report $model
 */
$this->title = 'Отчет';
$this->params['breadcrumbs'][] = ['label' => Report::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

?>
<div class="row">
    <div class="col-md-9">
        <?php $box = Box::begin(['cssClass' => 'box-primary', 'title' => '']) ?>

        <?= DetailView::widget(['model' => $model, 'attributes' => [
            'name',
            'created_at:datetime',
            'dealer.name',
            'profile.full_name',
        ]]); ?>

        <div style="margin-top:20px;">
            <?= Html::a(Icons::i('download'), ['download', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        </div>

        <?php $box = Box::end() ?>
    </div>
    <div class="col-md-3">
        <?php Box::begin(['cssClass' => 'box-primary', 'title' => 'Статус отчета']) ?>

        <h3 style="margin:0 0 40px;"><?= $model->renderStatusButton() ?></h3>

        <?php if ($model->adminCan(Report::STATUS_CONFIRMED)): ?>
            <a class="btn btn-primary btn-block"
               href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Report::STATUS_CONFIRMED]) ?>">
                <i class="fa fa-check"></i> Подтвердить отчет
            </a>
        <?php endif ?>

        <?php if ($model->adminCan(Report::STATUS_REJECTED)): ?>
            <a class="btn btn-danger btn-block"
               href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Report::STATUS_REJECTED]) ?>">
                <i class="fa fa-cross"></i> Отклонить отчет
            </a>
        <?php endif ?>

        <?php if ($model->adminCan(Report::STATUS_APPROVED)): ?>
            <a class="btn btn-success btn-block"
               href="<?= Url::to(['change-status', 'id' => $model->id, 'status' => Report::STATUS_APPROVED]) ?>">
                <i class="fa fa-check"></i> Одобрить отчет
            </a>
        <?php endif ?>

        <?php Box::end() ?>
    </div>
</div>
