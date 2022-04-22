<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 */

$this->params['body-class'] = 'main';

$css = <<<CSS
    .infoblock {
        position: relative;
        max-width: 400px;
    }
    .infoblock-bg {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background: black;
        opacity: 0.6;
        z-index: -1;
    }
    .infoblock-text {
        padding: 10px 15px 4px;
        color: white;
    }
CSS;

$this->registerCss($css);

?>

<div class="container">
	<div class="infoblock pull-right">
		<div class="infoblock-bg"></div>

		<div class="infoblock-text">
			<p>Приветствуем на сайте партнерской программы "Краски бонус"!</p>
			<p>Если Вы уже являетесь участником программы,
			пожалуйста, нажмите кнопку "Войти". Если Вы впервые на сайте, нажмите кнопку "Регистрация" и укажите свой
			номер сотового телефона</p>
		</div>
	</div>
</div>

