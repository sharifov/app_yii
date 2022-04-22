<?php
/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var Dealer $dealer
 * @var Promotion $promotion
 * @var Profile $recipient
 */

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yz\icons\Icons;

$this->title = 'Участники акции';
$this->params['breadcrumbs'][] = ['label' => "Дилер «{$dealer->name}» по акции «{$promotion->name}»", 'url' => ['/dashboard/index', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
$title = "Участники";
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['/sales/sellers', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]];
$this->params['breadcrumbs'][] = $recipient->full_name;

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

?>

<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>

<div class="container">
    <h3>Участник</h3>

    <div class="row">
        <div class="col-md-5">
            <?= DetailView::widget([
                'model' => $recipient,
                'attributes' => [
                    'full_name',
					'phone_mobile',
                    'purse.balance',
                ],
            ]) ?>
        </div>
    </div>

    <h3>Переводы на счет участника</h3>

    <div class="row">
        <div class="col-md-5">
            <?php echo GridView::widget([
                'id' => 'managers',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['attribute' => 'bonuses', 'label' => 'Бонусы'],
                    ['attribute' => 'created_at', 'label' => 'Дата перевода'],
                ],
            ]); ?>
        </div>
    </div>

    <h3>Зачислить на счет участника</h3>

    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'bonuses')->input('number')->label('Бонусы') ?>
        </div>
        <div class="col-md-2" style="margin-top:25px">
            <button type="submit" class="btn btn-primary">Зачислить</button>
        </div>
        <div class="help-block" style="margin-top:22px">
            Перевод будет осуществлен на указанную в поле сумму.<br/>
            Эта сумма будет списана с баланса ДЦ и зачислена на счет участника.
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
