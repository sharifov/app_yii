<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\PromotionSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\Promotion::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'promotion-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [/*['search'],*/ ['export', 'create', 'delete', 'return']],
            'gridId' => 'promotion-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\sales\common\models\Promotion',
        ]) ?>
    </div>

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'promotion-grid',
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
