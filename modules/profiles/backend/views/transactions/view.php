<?php
use yii\widgets\DetailView;
use yz\admin\widgets\FormBox;

/**
 * @var \yii\web\View $this
 * @var \ms\loyalty\finances\backend\forms\TransactionForm $model
 */
$this->title = 'Просмотр транзакции';
$this->params['breadcrumbs'][] = ['label' => marketingsolutions\finance\models\Transaction::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>

<?php  $box = FormBox::begin(['cssClass' => 'transaction-view box-primary', 'title' => '']) ?>

<?php $box->beginBody() ?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'created_at:datetime',
        'title',
        [
            'label' => 'Тип',
            'value' => \ms\loyalty\finances\common\models\Transaction::getTypeValues()[$model->type],
        ],
        'amountRubles',
        'comment',
    ],
]) ?>
<?php $box->endBody() ?>

<?php  FormBox::end() ?>