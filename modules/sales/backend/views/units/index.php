<?php

use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\UnitSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\Unit::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'unit-index box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [['export', 'create', 'delete', 'return']],
        'gridId' => 'unit-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'modules\sales\common\models\Unit',
    ]) ?>
</div>

<?= GridView::widget([
    'id' => 'unit-grid',
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => array_merge([
        ['class' => 'yii\grid\CheckboxColumn'],
    ], $columns, [
        [
            'class' => 'yz\admin\widgets\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ]),
]); ?>
<?php Box::end() ?>
