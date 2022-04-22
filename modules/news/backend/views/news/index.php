<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\icons\Icons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\news\backend\models\NewsSearch $searchModel
 * @var array $columns
 */

$this->title = modules\news\common\models\News::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'news-index box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [/*['search'],*/
            ['export', 'create', 'delete', 'return']],
        'gridId' => 'news-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'modules\news\common\models\News',
    ]) ?>
</div>

<?php //echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'id' => 'news-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => array_merge([
        ['class' => 'yii\grid\CheckboxColumn'],
    ], $columns, [
        [
            'class' => 'yz\admin\widgets\ActionColumn',
            'template' => '{update} {delete}',
            'buttons' => [
                'push' => function ($url, \modules\news\common\models\News $model) {
                    return Html::a(Icons::i('send'), $url, [
                        'title' => 'PUSH-уведомление',
                        'data-confirm' => 'Вы действительно хотите сделать PUSH-уведомление всем участникам?',
                        'data-method' => 'post',
                        'class' => 'btn btn-default btn-sm',
                        'data-pjax' => '0',
                    ]);
                },
            ],
        ],
    ]),
]); ?>
<?php Box::end() ?>
