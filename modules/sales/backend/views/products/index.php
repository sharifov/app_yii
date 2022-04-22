<?php

use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\ProductSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\Product::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'product-index box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [['export', 'create', 'delete', 'return']],
        'gridId' => 'product-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'modules\sales\common\models\Product',
    ]) ?>
</div>

<?= GridView::widget([
    'id' => 'product-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
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
