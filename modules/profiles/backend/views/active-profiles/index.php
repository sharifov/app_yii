<?php

use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\profiles\backend\models\ActiveProfilesSearch $searchModel
 * @var array $columns
 */

$this->title = 'Отчет об активных участниках';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'dealer-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [['export', 'return']],
            'gridId' => 'dealer-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'modules\profiles\common\models\Dealer',
        ]) ?>
    </div>


    <?= GridView::widget([
        'id' => 'dealer-grid',
        'dataProvider' => $dataProvider,
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
