<?php

use modules\news\common\models\News;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\news\backend\models\NewsSearch $searchModel
 * @var array $columns
 */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

News::isReadMessage(\Yii::$app->user->identity->profile->id, false);
?>

<div class="row">

    <div class="col-md-12">

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => 'partials/_publication',
        ]); ?>

    </div>

</div>
