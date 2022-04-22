<?php

use modules\profiles\backend\models\ProfileTransaction;
use modules\profiles\backend\models\ProfileTransactionSearch;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var ProfileTransactionSearch $searchModel
 * @var array $columns
 */

$this->title = 'Транзакции бонусных баллов';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'profile-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [['export', 'return']],
            'gridId' => 'profile-transactions-grid',
            'searchModel' => $searchModel,
            'modelClass' => ProfileTransaction::class,
        ]) ?>
    </div>

    <?= GridView::widget([
        'id' => 'profile-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>
<?php Box::end() ?>
