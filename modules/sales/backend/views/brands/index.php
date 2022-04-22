<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\BrandSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\Brand::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'brand-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [/*['search'],*/ ['export', 'create', 'delete', 'return']],
            'gridId' => 'brand-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\sales\common\models\Brand',
        ]) ?>
    </div>

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'brand-grid',
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
