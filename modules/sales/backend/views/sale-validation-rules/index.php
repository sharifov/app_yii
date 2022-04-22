<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\SaleValidationRuleSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\SaleValidationRule::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'sale-validation-rule-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [['create', 'delete', 'return']],
            'gridId' => 'sale-validation-rule-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\sales\common\models\SaleValidationRule',
        ]) ?>
    </div>

    <?= GridView::widget([
        'id' => 'sale-validation-rule-grid',
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
