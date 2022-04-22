<?php
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\Report;
use modules\sales\common\models\Promotion;
use ms\loyalty\catalog\common\models\CatalogSettings;
use ms\loyalty\catalog\frontend\widgets\CatalogDashboard;
use ms\loyalty\prizes\payments\frontend\widgets\PaymentsDashboard;
use yii\helpers\Html;
use yii\helpers\Url;
use yz\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var Profile $profile
 * @var integer $dealer_id
 * @var integer $promotion_id
 * @var Dealer $dealer
 * @var Promotion $promotion
 */
$profile = \Yii::$app->user->identity->profile;
$dealers = $profile->dealers;
$activeReport = $profile->activeReport;

$js = <<<JS
    $(document).ready(function() {
        $('#dealer').change(function() {
            window.location = '/dashboard/index?dealer_id=' + $(this).val();
        });
        $('#promotion').change(function() {
            window.location = '/dashboard/index?dealer_id=' + $('#dealer').val() + '&promotion_id=' + $(this).val();
        });
    });
JS;

$this->registerJs($js);
?>

<?php if ($dealer && $promotion): ?>
	<?= $this->render('@app/views/partials/_dealer', compact('dealer', 'promotion')); ?>
<?php endif; ?>

<?= \ms\loyalty\theme\widgets\InterfaceWidgets::show('profile') ?>

<?= $this->render('@app/views/partials/_rules', compact('profile')); ?>

<?php if (!$person): ?>
    <div class="alert alert-info" style="margin-left:14px !important;">
        <p><strong>Уважаемый участник!</strong></p>

        <p>Для использования витрины призов и перевода бонусов необходимо заполнить/дозаполнить Вашу анкету для подачи НДФЛ.</p>

        <p>Пожалуйста, перейдите по <a href="/taxes/account/register">ссылке</a></p>
    </div>
<?php endif; ?>

<?php if ($profile->isManager()): ?>
	<div class="container">
		<h3>Дилеры и акции</h3>
	</div>

	<div class="container">
		<div class="panel panel-<?= ($dealer && $promotion) ? 'success' : 'default' ?>">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-4" id="name_dealer_input">
						<label class="control-label">Выберите дилера</label>
						<?= Html::dropDownList('dealer_id', $dealer_id, Dealer::getOptionsByProfileId($profile->id), ['class' => 'form-control', 'id' => 'dealer', 'prompt' => 'выберите']) ?>
					</div>
					<div class="col-md-4" id="action_name_dealer" style="display: none;">
						<label class="control-label">Выберите акцию</label>
						<?= Html::dropDownList('promotion_id', $promotion_id, Promotion::getOptionsByDealer($dealer_id), ['class' => 'form-control', 'id' => 'promotion', 'prompt' => 'выберите']) ?>
					</div>
				</div>
			</div>
			<?php if ($promotion && $dealer): ?>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3">
							<a class="btn btn-default"
							   href="<?= Url::to(['/sales/sellers', 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]) ?>">
								<i class="fa fa-users"></i> Список участников
							</a>
						</div>
						<div class="col-md-3">
							<a class="btn btn-primary"
							   href="<?= Url::to(['/sales/sales/app', 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]) ?>">
								<i class="fa fa-shopping-cart"></i> Оформить продажу
							</a>
						</div>
						<div class="col-md-3">
							<a class="btn btn-default"
							   href="<?= Url::to(['/sales/sales/index', 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]) ?>">
								<i class="fa fa-list"></i> Список совершенных продаж
							</a>
						</div>
						<div class="col-md-3">
							<a class="btn btn-default pull-right"
							   href="<?= Url::to(['/profiles/rules/index', 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]) ?>">
								<i class="fa fa-list"></i> Памятка акции
							</a>
						</div>
					</div>
				</div>
			<?php endif ?>
		</div>
	</div>

	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				Отчет об остатках отчет об остатках за текущий месяц
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<?php if (!$activeReport || in_array($activeReport->status, [Report::STATUS_REJECTED, Report::STATUS_NEW])): ?>
							<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
							<div class="row">
								<div class="col-md-6">
									<?= $form->field($model, 'fileUpload')->fileInput()->label('') ?>
								</div>
								<div class="col-md-6">
									<button type="submit" class="btn btn-primary">Прикрепить</button>
								</div>
							</div>
							<?php ActiveForm::end() ?>
						<?php endif; ?>
					</div>
					<div class="col-md-6">
						<?php if ($activeReport && ($activeReport->status == null || $activeReport->status == Report::STATUS_NEW)): ?>
							<b style="color:green"><i class="fa fa-check"></i> Отчет был загружен и ожидает одобрения
								модератором</b>
						<?php endif; ?>

						<?php if ($activeReport && $activeReport->status == Report::STATUS_CONFIRMED): ?>
							<b style="color:green"><i class="fa fa-check"></i> Отчет был проверен модератором, и ожидает
								одобрения администратором</b>
						<?php endif; ?>

						<?php if ($activeReport && $activeReport->status == Report::STATUS_APPROVED): ?>
							<b style="color:green"><i class="fa fa-check"></i> Отчет был одобрен администратором</b>
						<?php endif; ?>

						<?php if ($activeReport && $activeReport->status == Report::STATUS_REJECTED): ?>
							<p><b style="color:maroon"><i class="fa fa-asterisk"></i>
									Внимание! Ваш отчет не прошел проверку модератом. Пожалуйтста, загрузите другой.</b>
							</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php if ($person): ?>

    <div class="container">
        <h3>Призы</h3>
    </div>

    <div class="container">
        <h3>Перевод на электронные кошельки и мобильные телефоны</h3>
    </div>
    <?= PaymentsDashboard::widget() ?>

    <div class="container">
        <h3>Электронные Подарочные Сертификаты</h3>
    </div>
    <?php if (!empty(CatalogSettings::get()->notification_text)): ?>
        <div style="margin: 0 0 10px 16px; color: red"><?= CatalogSettings::get()->notification_text ?></div>
    <?php endif; ?>
    <?= CatalogDashboard::widget() ?>
<?php endif;?>
