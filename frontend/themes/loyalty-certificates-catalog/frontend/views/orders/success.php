<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \ms\loyalty\catalog\common\models\CatalogOrder $order
 * @var \ms\loyalty\contracts\prizes\PrizeRecipientInterface $catalogUser
 */
$time = Yii::$app->getModule('catalog')->zakazpodarkaOrderDelay;
if ($time > 60 * 60) {
	$time = ceil($time / 3600) . (\Yii::$app->session->get('lang', 'ru') == 'en' ? ' hours' : ' часов');
}
elseif ($time > 60) {
	$time = ceil($time / 60) . (\Yii::$app->session->get('lang', 'ru') == 'en' ? ' minutes' : ' минут');
}
else {
	$time = ceil($time) . (\Yii::$app->session->get('lang', 'ru') == 'en' ? ' seconds' : ' секунд');
}

$this->title = \Yii::$app->session->get('lang', 'ru') == 'en' ? 'Order is completed!' : 'Заказ успешно совершен!';
$this->params['header'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>

<div class="alert alert-info">
	Максимальный срок доставки сертификата на электронную почту составляет 8 рабочих дней
</div>

<p><?= \Yii::$app->session->get('lang', 'ru') == 'en' ? 'Order ID' : 'Номер Вашего заказа' ?>:
	<strong><?= $order->id ?></strong>
</p>


<?php if (\Yii::$app->session->get('lang', 'ru') == 'en'): ?>
	<div>
		<small>Summary cards: <?= $order->amount ?></small>
	</div>
	<?php if ($order->taxes_profile): ?>
		<div>
			<small>Summary taxes: <?= $order->taxes_profile ?></small>
		</div>
	<?php endif ?>
<?php else: ?>
	<div>
		<small>Сумма товаров: <?= $order->amount ?> баллов</small>
	</div>
	<?php if ($order->taxes_profile): ?>
		<div>
			<small>Сумма налога НДФЛ: <?= $order->taxes_profile ?></small>
		</div>
	<?php endif ?>
<?php endif ?>
<p>
	<?= \Yii::$app->session->get('lang', 'ru') == 'en' ? 'Total amount' : 'Общая сумма заказа' ?>:
	<strong><?= $order->profile_amount ?>
		<?= \Yii::$app->session->get('lang', 'ru') == 'en' ? '' : 'баллов' ?>
	</strong>
</p>

<p>
	<?= \Yii::$app->session->get('lang', 'ru') == 'en' ? 'Current balance' : 'На Вашем счету осталось' ?>:
	<strong><?= $catalogUser->recipientPurse->balance ?>
		<?= \Yii::$app->session->get('lang', 'ru') == 'en' ? '' : 'баллов' ?>
	</strong>
</p>

<p>
	<?php if (\Yii::$app->session->get('lang', 'ru') == 'en'): ?>
		You will recieve ordered cards to E-mail <?= Html::encode($order->delivery_email) ?> in 8 days.
	<?php else: ?>
		В течении 8 рабочих дней Вы получите на электронный адрес <?= Html::encode($order->delivery_email) ?>
		заказанные сертификаты. Также Вы сможете скачать их в разделе
		"<a href="<?= Url::to(['index']) ?>" target="_blank">Заказы</a>".
	<?php endif; ?>
</p>

<p></p>
<?php if ($order->is_allow_cancel): ?>
	<?php if (\Yii::$app->session->get('lang', 'ru') == 'en'): ?>
		<p>You are able to cancel this order <a href="<?= Url::to(['index']) ?>" target="_blank">here</a>
			in <?= $time ?>.
		</p>
	<?php else: ?>
		<p>Вы также можете отменить данный заказ на странице
			"<a href="<?= Url::to(['index']) ?>" target="_blank">Заказы</a>"
			в течении <?= $time ?>.
		</p>
	<?php endif; ?>
<?php endif ?>