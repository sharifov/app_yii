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
 * @var string $promotionType
 */

\modules\sales\common\app\assets\SaleApp2Asset::register($this);

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
    'promotion_id' => $promotionId,
    'promotion_type' => $promotionType,
    'dealer_id' => $dealerId,
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

        <div class="panel panel-default" ng-show="false">
            <div class="panel-heading">
                Выберите форму заполнения отчета
            </div>
            <div class="panel-body">
                <div class="form-group" style="padding-left:16px">
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="sku" ng-model="type" ng-change="typeDefault()">
                            Товарные позиции
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="brand_kg" ng-model="type" ng-change="typeBrands()">
                            Торговые марки, кг.
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="brand_rub" ng-model="type" ng-change="typeBrandsRub()">
                            Торговые марки, руб.
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" ng-show="type == 'sku'">
            <div class="panel-heading">
                Товарные позиции
            </div>
            <div class="panel-body">

                <table class="table" ng-show="model.positions.length > 0" ng-cloak>
                    <tr>
                        <th class="col-md-2">Тип</th>
                        <th class="col-md-3">Вид</th>
                        <th class="col-md-5">Товар</th>
                        <th class="col-md-1">Шт</th>
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

                                <div class="input-group-addon" ng-show="position.type">шт</div>
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

        <div class="panel panel-default" ng-show="type == 'brand_kg'">
            <div class="panel-heading">
                Торговые марки, кг.
            </div>
            <div class="panel-body">

                <table class="table" ng-show="model.brandPositions.length > 0" ng-cloak>
                    <tr>
                        <th class="col-md-4">Бренд</th>
                        <th class="col-md-1">кг</th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tr ng-repeat="brandPosition in model.brandPositions">
                        <td>
                            <select class="form-control" ng-model="brandPosition.brand_id">
                                <option ng-repeat="brand in brands" value="{{ brand.id }}">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <input class="form-control" type="number"
                                       ng-model="brandPosition.kg"
                                       ng-disabled="!brandPosition.brand_id"/>

                                <div class="input-group-addon">кг</div>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" ng-click="removeBrandPosition(brandPosition)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </table>

                <button type="button" class="btn btn-success" ng-click="addBrandPosition()">
                    <i class="fa fa-plus"></i> Добавить позицию
                </button>
            </div>
        </div>

        <div class="panel panel-default" ng-show="type == 'brand_rub'">
            <div class="panel-heading">
                Торговые марки, руб.
            </div>
            <div class="panel-body">

                <table class="table" ng-show="model.brandPositions.length > 0" ng-cloak>
                    <tr>
                        <th class="col-md-4">Бренд</th>
                        <th class="col-md-1">руб.</th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tr ng-repeat="brandPosition in model.brandPositions">
                        <td>
                            <select class="form-control" ng-model="brandPosition.brand_id">
                                <option ng-repeat="brand in brands" value="{{ brand.id }}">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <input class="form-control" type="number"
                                       ng-model="brandPosition.rub"
                                       ng-disabled="!brandPosition.brand_id"/>

                                <div class="input-group-addon">руб.</div>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" ng-click="removeBrandPosition(brandPosition)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </table>

                <button type="button" class="btn btn-success" ng-click="addBrandPosition()">
                    <i class="fa fa-plus"></i> Добавить позицию
                </button>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Подтверждающие документы</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2" ng-repeat="document in model.documents">
                        <div class="thumbnail">
                            <a href="/sales/sales/download-document?id={{ document.id }}" target="_blank">
                                <img ng-if="document.isImage" src="/sales/sales/download-document?id={{ document.id }}" />
                                <span ng-if="!document.isImage">{{ document.original_name }}</span>
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

        <div class="alert alert-danger" ng-show="errors.length > 0" ng-cloak>
            <strong><i class="fa fa-exclamation-circle fa-lg"></i></strong> Перечень обнаруженных ошибок приведен в начале страницы<br>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary"
                        ng-click="save('<?= Statuses::ADMIN_REVIEW ?>')"
                        ng-disabled="(model.positions.length == 0 && model.brandPositions.length == 0) || disabled">
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
                <div class="panel-body">
                    <small>{{ type }}</small>
                </div>
            </div>
        <?php endif ?>

    </form>
</div>

