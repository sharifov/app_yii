<?php


use marketingsolutions\finance\models\Purse;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yz\admin\grid\GridView;
use yz\admin\widgets\Box;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var array $columns
 */

$this->title = Purse::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

$box = Box::begin(['cssClass' => 'profile-index box-primary']);

?>

<?= GridView::widget([
    'id' => 'profile-grid',
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'showFooter' => true
]); ?>
<?php Box::end() ?>

