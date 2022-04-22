<?php

use modules\sales\common\models\Sale;
use yii\helpers\Html;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;
use yz\icons\Icons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\SaleSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\Sale::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'sale-index box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [['createSale'], ['search'], ['export', 'delete', 'return']],
        'gridId' => 'sale-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'modules\sales\common\models\Sale',
        'buttons' => [
            'createSale' => [
                'label' => 'Создать',
                'icon' => Icons::o('plus'),
                'route' => ['sales/create-sale'],
                'class' => 'btn btn-success',
            ],
        ],
    ]) ?>
</div>

<?= GridView::widget([
    'id' => 'sale-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => array_merge([
        ['class' => 'yii\grid\CheckboxColumn'],
    ], $columns, [
        [
            'class' => 'yz\admin\widgets\ActionColumn',
            'template' => '{view} {delete}',
            'buttons' => [
                'delete' => function ($url, Sale $model, $key) {
                    if ($model->statusManager->canBeDeleted() == false) {
                        return '';
                    }

                    return Html::a(Icons::i('trash-o fa-lg'), $url, [
                        'title' => Yii::t('admin/t', 'Delete'),
                        'data-confirm' => Yii::t('admin/t', 'Are you sure to delete this item?'),
                        'data-method' => 'post',
                        'class' => 'btn btn-danger btn-sm',
                        'data-pjax' => '0',
                    ]);
                }
            ]
        ],
    ]),
]); ?>
<?php Box::end() ?>
