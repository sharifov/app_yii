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
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Продажа за 2015 год</div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <input class="form-control" type="number" ng-model="model.sale.kg"/>

                            <div class="input-group-addon">кг</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2" ng-repeat="document in model.documents">
                        <div class="thumbnail">
                            <a href="/sales/sales/download-document?id={{ document.id }}" target="_blank">

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
                            <i class="fa fa-plus"></i> Добавить подтверждающий документ
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Продажа за 2014 год. Пропустите, если таковой нет.</div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <input class="form-control" type="number" ng-model="model.sale.previous_kg"/>

                            <div class="input-group-addon">кг</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2" ng-repeat="document in model.previous_documents">
                        <div class="thumbnail">
                            <a href="/sales/sales/download-previous-document?id={{ document.id }}" target="_blank">

                                <img src="/sales/sales/download-previous-document?id={{ document.id }}"
                                     ng-if="document.isImage"/>

                                <span ng-if="!document.isImage">
                                    {{ document.original_name }}
                                </span>
                            </a>


                            <div class="btn btn-default"
                                 ngf-select=""
                                 ng-model="document.document"
                                 ngf-change="document.uploadPrevious()"
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
                                        ng-click="removePreviousDocument(document)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-success" ng-click="addPreviousDocument()">
                            <i class="fa fa-plus"></i> Добавить подтверждающий документ
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

