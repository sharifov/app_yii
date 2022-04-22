<?php
/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var Dealer $dealer
 * @var Promotion $promotion
 */

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use yii\grid\GridView;
use yii\helpers\Html;
use yz\icons\Icons;

$this->title = "Участники";
$this->params['breadcrumbs'][] = ['label' => "Дилер «{$dealer->name}» по акции «{$promotion->name}»", 'url' => ['/dashboard/index', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
$this->params['breadcrumbs'][] = $this->title;

$css = <<<CSS
	#managers {
		max-width: 700px;
	}
	#managers table, #managers table th {
	    border: none;
	}
	#managers .summary {
	    display: none;
	}
CSS;

$this->registerCss($css);
?>

<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>

<div class="container">
	<h3><?= $this->title ?></h3>

	<?php echo GridView::widget([
		'id'           => 'managers',
		'dataProvider' => $dataProvider,
		'columns'      => [
			'phone_mobile',
			'full_name',
			[
				'label'          => 'Баллы',
				'contentOptions' => ['style' => 'width: 200px;'],
				'value' => function (Profile $model) {
					$purse = $model->purse;

					return $purse && $model->identity ? $purse->balance : '0';
				}
			],
			[
				'header' => 'Операции по счету',
				'format' => 'html',
				'value' => function(Profile $model) use ($dealer, $promotion) {
					return $model->identity
						? Html::a(Icons::i('dollar', ['style' => 'width:30px']), ['view', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id,'id' => $model->id], ['class' => 'btn btn-xs btn-primary'])
						: 'не зарегистрирован';
				},
			],
		],
	]); ?>
</div>

