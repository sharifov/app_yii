<?php

use ms\loyalty\catalog\common\models\CatalogSettings;
use ms\loyalty\catalog\frontend\widgets\Cards;

/**
 * @var \yii\web\View $this
 */

$title = \Yii::$app->session->get('lang', 'ru') == 'en' ? 'Catalog' : 'Каталог электронных сертификатов';
$this->params['header'] = $title;
$this->params['breadcrumbs'][] = $title;
?>

	<div class="shopping-cart-container">
		<div class="shopping-cart-container-inner">
			<?= \ms\loyalty\catalog\frontend\widgets\Cart::widget() ?>
		</div>
	</div>
<?php
$profileId = \modules\profiles\common\models\Profile::findOne(['identity_id'=> \Yii::$app->user->identity->id])->id;
$person = \modules\profiles\common\models\Profile::isNdflRecord($profileId);
?>
<?php if($person):?>
    <?php if (!empty(CatalogSettings::get()->notification_text)): ?>
        <div style="margin: 10px 0 10px 19px; color: red"><?= CatalogSettings::get()->notification_text ?></div>
    <?php endif; ?>
    <?php echo Cards::widget() ?>
<?php else:?>
    <div class="alert alert-info" style="margin-left:14px !important;">
        <p><strong>Уважаемый участник!</strong></p>

        <p>Для использования витрины призов и перевода бонусов необходимо заполнить/дозаполнить Вашу анкету для подачи НДФЛ.</p>

        <p>Пожалуйста, перейдите по <a href="/taxes/account/register">ссылке</a></p>
    </div>
<?php endif;?>
