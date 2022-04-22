<?php

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use modules\sales\common\sales\statuses\Statuses;

/**
 * @var int | null $id
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var array $config
 * @var int $dealerId
 * @var int $promotionId
 */

\modules\sales\common\app\assets\SaleAppAsset::register($this);

$profile = Profile::findOne(Yii::$app->user->identity->id);
$saveUrl = is_null($id) ? '/create' : '/update';

if ($id !== null) {
	/** @var Sale $sale */
	$sale = Sale::findOne($id);
	$dealer = $sale->dealer;
	$promotion = $sale->promotion;
} else {
	$dealer = Dealer::findOne($dealerId);
	$promotion = Promotion::findOne($promotionId);
}

$saveUrl .= '?dealer_id='.$dealerId.'&promotion_id='.$promotionId;

$settings = \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::merge([
	'id' => $id,
	'saveUrl' => $saveUrl,
], $config));

$js = <<<JS
saleEditApplication.configure({$settings});
JS;
$this->registerJs($js, \yii\web\View::POS_END);

$css = <<<CSS
    .info-text {
        padding-top: 7px;
    }
CSS;
$this->registerCss($css);

?>

<div class="sale-form" ng-app="SaleEditApplication" ng-controller="SaleEdit">

	<div class="alert alert-danger" ng-show="errors.length > 0" ng-cloak>
		<strong><i class="fa fa-exclamation-circle fa-lg"></i></strong> Обнаружены следующие ошибки:<br><br>
		<ul>
			<li ng-repeat="error in errors">{{ error.message }}</li>
		</ul>
	</div>

	<form class="form-horizontal">

		<div class="panel panel-default">
			<div class="panel-heading">
				Общая информация
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-md-3">Дилер</label>

					<div class="col-md-6">
						<input class="form-control" type="text" value="<?= $dealer->name ?>" disabled="disabled"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Акция</label>

					<div class="col-md-6">
						<input class="form-control" type="text" value="<?= $promotion->name ?>" disabled="disabled"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Участвующие бренды</label>

					<div class="col-md-6">
						<input class="form-control" type="text" value="<?= $promotion->brand_names ?>" disabled="disabled"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Дата продажи</label>

					<div class="col-md-2">
						<?= \marketingsolutions\widgets\DatePicker::widget([
							'dateFormat'   => 'dd.MM.yyyy',
							'name'         => '',
							'options'      => [
								'class'    => 'form-control',
								'ng-model' => 'model.sale.sold_on_local',
							],
							'clientEvents' => [
								'changeDate' => 'function (e) {
                            angular.element((e.target||e.srcElement)).triggerHandler("input");
                        }'
							]
						]) ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				Товарные позиции
			</div>
			<div class="panel-body">

				<table class="table" ng-show="model.positions.length > 0" ng-cloak>
					<tr>
						<th class="col-md-2">Тип</th>
						<th class="col-md-3">Вид</th>
						<th class="col-md-5">Товар</th>
						<th class="col-md-1">На сумму в рублях</th>
						<th class="col-md-1"></th>
					</tr>
					<tr ng-repeat="position in model.positions">
						<td>
							<select class="form-control"
									ng-model="position.type_id"
									ng-change="position.changeType()">
								<option ng-repeat="type in types" value="{{ type.id }}">
									{{ type.name }}
								</option>
							</select>
						</td>
						<td>
							<select class="form-control"
									ng-model="position.category_id"
									ng-change="position.changeCategory()"
									ng-disabled="!position.type_id">
								<option ng-repeat="category in position.categories" value="{{ category.id }}">
									{{ category.name }}
								</option>
							</select>
						</td>
						<td>
							<select class="form-control"
									ng-model="position.product_id"
									ng-change="position.changeProduct()"
									ng-disabled="!position.type_id || !position.category_id || position.products.length == 0">
								<option ng-repeat="product in position.products"
										value="{{ product.id }}">
									{{ product.name }}
								</option>
							</select>
						</td>
						<td>
							<div class="input-group">
								<input class="form-control" type="number"
									   ng-model="position.kg"
									   ng-disabled="!position.product"/>
							</div>
						</td>
						<td>
							<button type="button" class="btn btn-danger" ng-click="removePosition(position)">
								<i class="fa fa-trash"></i>
							</button>
						</td>
					</tr>
				</table>

				<button type="button" class="btn btn-success" ng-click="addPosition()">
					<i class="fa fa-plus"></i> Добавить позицию
				</button>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">

				Подтверждающие документы

			</div>
			<div class="panel-body">

				<div class="row">
					<div class="col-md-2" ng-repeat="document in model.documents">
						<div class="thumbnail">
							<a href="/sales/sales/download-document?id={{ document.id }}" target="_blank"
							   ng-if="document.uploaded">

								<img src="/sales/sales/download-document?id={{ document.id }}"
									 ng-if="document.isImage"/>

                                <span ng-if="!document.isImage">
                                    {{ document.original_name }}
                                </span>
							</a>


							<div class="btn btn-default"
								 ngf-select=""
								 ng-model="document.document"
								 ngf-change="document.upload()"
								 ng-disabled="document.uploading"
								 ng-show="! document.uploaded"
								 ngf-multiple="false"
								>
								<i class="fa fa-btn fa-spinner fa-spin" ng-if="document.uploading"></i>
								Выбрать файл
							</div>

							<div class="caption">

								<div ng-show="document.errors.length > 0">
                                    <span class="label label-danger" ng-repeat="error in document.errors">
                                        {{ error.message }}
                                    </span>
								</div>

								<button type="button" class="btn btn-danger btn-sm"
										ng-click="removeDocument(document)">
									<i class="fa fa-trash"></i>
								</button>
							</div>
						</div>
					</div>

					<div class="col-md-2">
						<button type="button" class="btn btn-success" ng-click="addDocument()">
							<i class="fa fa-plus"></i> Добавить документ
						</button>
					</div>
				</div>

			</div>
		</div>

		<hr/>

		<div class="form-group">
			<div class="col-md-12">
				<button type="button" class="btn btn-primary"
						ng-click="save('<?= Statuses::ADMIN_REVIEW ?>')"
						ng-disabled="model.positions.length == 0 || disabled">
					<i class="fa fa-btn fa-check" ng-if=" ! disabled"></i>
					<i class="fa fa-btn fa-spinner fa-spin" ng-if="disabled"></i>
					Отправить на проверку администратору
				</button>

				<button type="button" class="btn btn-default" ng-click="save('<?= Statuses::DRAFT ?>')"
						ng-disabled="model.positions.length == 0 || disabled">
					<i class="fa fa-btn fa-circle-o" ng-if=" ! disabled"></i>
					<i class="fa fa-btn fa-spinner fa-spin" ng-if="disabled"></i>
					Сохранить в черновики
				</button>
			</div>
		</div>

		<?php if (YII_ENV_DEV): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					Отладочная информация
				</div>
				<div class="panel-body">
					<small>{{ model }}</small>
				</div>
			</div>
		<?php endif ?>

	</form>
</div>

