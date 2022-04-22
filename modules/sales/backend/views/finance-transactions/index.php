<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\sales\backend\models\FinanceTransactionsSearch $searchModel
 * @var array $columns
 */

$this->title = modules\sales\common\models\FinanceTransactions::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'finance-transactions-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [/*['search'],*/ ['export','return']],
            'gridId' => 'finance-transactions-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\sales\common\models\FinanceTransactions',
        ]) ?>
    </div>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'finance-transactions-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => array_merge([
            ['class' => 'yii\grid\CheckboxColumn'],
        ], $columns, [
            [
                'class' => 'yz\admin\widgets\ActionColumn',
                'template' => '',
            ],
        ]),
    ]); ?>
<?php Box::end() ?>
