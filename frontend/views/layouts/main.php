<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
\frontend\assets\AppAsset::register($this);

$updateBrowserJs = <<<'JS'
var $buoop = {c:2};
function $buo_f(){
 var e = document.createElement("script");
 e.src = "//browser-update.org/update.min.js";
 document.body.appendChild(e);
}
try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
catch(e){window.attachEvent("onload", $buo_f)}

JS;
$this->registerJs($updateBrowserJs, $this::POS_HEAD);

$yandexMetrika = <<<'HTML'
(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter31471403 = new Ya.Metrika({ id:31471403, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");
HTML;
$this->registerJs($yandexMetrika, $this::POS_END);

?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= Html::encode($this->title) ?></title>
		<?= Html::csrfMetaTags() ?>
		<?php $this->head() ?>
	</head>
	<body class="<?= ArrayHelper::getValue($this->params, 'body-class', 'body-default') ?>">
	<?php $this->beginBody() ?>

	<div class="wrap">
		<div class="navbar navbar-default">
			<div class="container">
				<a class="header__logo pull-left" href="/">
					<img class="logo-header" src="/images/logo2.png" alt="" style="max-width:320px;"/>
				</a>

				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
								data-target="#top-nav">
							<span class="sr-only">Отобразить навигацию</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="collapse navbar-collapse" id="top-nav">
						<?= \yii\widgets\Menu::widget([
							'options' => [
								'class' => 'nav navbar-nav b-nav-group pull-right registered'
							],
							'items' => [
                                ['label' => 'Новости', 'url' => ['/news/news/index'], 'visible' => !Yii::$app->user->isGuest],
								['label' => 'Войти', 'url' => ['/identity/auth/login'], 'visible' => Yii::$app->user->isGuest],
								['label' => 'Регистрация', 'url' => ['/identity/auth/register'], 'visible' => Yii::$app->user->isGuest],
								['label' => 'Помощь', 'url' => ['/feedback/messages/add']],
								['label' => 'Об акции', 'url' => ['@frontendWeb/media/uploads/{$promotion->id}.pdf'], 'visible' => !Yii::$app->user->isGuest],
								['label' => 'Выйти', 'url' => ['/identity/auth/logout'], 'visible' => !Yii::$app->user->isGuest],
							]
						]) ?>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<?= Breadcrumbs::widget([
				'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
			]) ?>
		</div>

		<div class="container">
			<div class="page-content">
				<?= \ms\loyalty\theme\frontend\widgets\Alerts::widget() ?>
				<?= $content ?>
			</div>
		</div>
	</div>

	<footer class="footer">
		<div class="footer-inner">
			<div class="container">
				<div class="pull-left">
					<p>&copy; Маркетинг Солюшнз<br/>
						Телефон горячей линии 8 800 500-82-46<br/>
						с 9:00 до 18:00 по московскому времени
					</p>
				</div>
			</div>
		</div>
	</footer>

	<?php $this->endBody() ?>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function(d, w, c) {
			(w[c] = w[c] || []).push(function() {
				try {
					w.yaCounter38309250 = new Ya.Metrika({
						id:                  38309250,
						clickmap:            true,
						trackLinks:          true,
						accurateTrackBounce: true
					});
				} catch (e) {
				}
			});

			var n = d.getElementsByTagName("script")[0],
				s = d.createElement("script"),
				f = function() { n.parentNode.insertBefore(s, n); };
			s.type = "text/javascript";
			s.async = true;
			s.src = "https://mc.yandex.ru/metrika/watch.js";

			if (w.opera == "[object Opera]") {
				d.addEventListener("DOMContentLoaded", f, false);
			} else {
				f();
			}
		})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript>
		<div><img src="https://mc.yandex.ru/watch/38309250" style="position:absolute; left:-9999px;" alt=""/></div>
	</noscript>
	<!-- /Yandex.Metrika counter -->

	</body>
	</html>
<?php $this->endPage() ?>