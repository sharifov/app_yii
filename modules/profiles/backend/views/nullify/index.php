<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\profiles\backend\models\NullifySearch $searchModel
 * @var array $columns
 */

$this->title = modules\profiles\common\models\Nullify::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'nullify-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [/*['search'],*/ ['export', 'create', 'delete', 'return']],
            'gridId' => 'nullify-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\profiles\common\models\Nullify',
        ]) ?>
    </div>

    <?= GridView::widget([
        'id' => 'nullify-grid',
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
