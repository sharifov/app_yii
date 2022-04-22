<?php

use ms\loyalty\finances\common\components\CompanyAccount;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var ms\loyalty\finances\backend\models\TransactionSearch $searchModel
 * @var array $columns
 */

$this->title = marketingsolutions\finance\models\Transaction::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>

<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= CompanyAccount::getPurse()->balance / 100 ?></h3>
                <p>Текущий баланс счета</p>
            </div>
            <div class="icon">
                <i class="fa fa-rub"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= CompanyAccount::calculateTransactionsSum(\marketingsolutions\finance\models\Transaction::INCOMING) / 100 ?></h3>
                <p>Сумма входящих транзакций</p>
            </div>
            <div class="icon">
                <i class="fa fa-rub"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?= CompanyAccount::calculateTransactionsSum(\marketingsolutions\finance\models\Transaction::OUTBOUND) / 100 ?></h3>
                <p>Сумма исходящих транзакций</p>
            </div>
            <div class="icon">
                <i class="fa fa-rub"></i>
            </div>
        </div>
    </div>
</div>

<?php $box = Box::begin(['cssClass' => 'transaction-index box-primary']) ?>
    <div class="text-right">
        <?php echo ActionButtons::widget([
            'order' => [['search'], ['export', 'create', 'delete', 'return']],
            'gridId' => 'transaction-grid',
            'searchModel' => $searchModel,
            'modelClass' => 'marketingsolutions\finance\models\Transaction',
        ]) ?>
    </div>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'transaction-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => array_merge([
            ['class' => 'yii\grid\CheckboxColumn'],
        ], $columns, [
            [
                'class' => 'yz\admin\widgets\ActionColumn',
                'template' => '{view}',
            ],
        ]),
    ]); ?>
<?php Box::end() ?>
