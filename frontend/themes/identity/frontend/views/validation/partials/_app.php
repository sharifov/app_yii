<?php
use frontend\assets\PhoneValidationAsset;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $validateCodeText
 */

PhoneValidationAsset::register($this);

?>

<div class="container-fluid" ng-app="PhoneValidation" ng-controller="Validation">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Введите код подтверждения, который будет отправлен на Ваш номер сотового телефона и заполните анкету участника
                </div>
                <div class="panel-body">

                    <div class="alert alert-danger" ng-show="errors.length > 0" ng-cloak>
                        <strong>Внимание!</strong> Обнаружены следующие ошибки:
                        <ul>
                            <li ng-repeat="error in errors">
                                {{ error.message }}
                            </li>
                        </ul>
                    </div>

                    <div ng-show="! tokenRequested">

                        <form class="form-horizontal">

                            <div class="form-group">
                                <?= Html::label('Номер телефона', null, ['class' => 'col-md-3 control-label']) ?>
                                <div class="col-md-5">

                                    <input type="text" class="form-control"
                                           ng-model="phone"
                                           input-mask="{mask: '+7 999 999-99-99'}"/>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-offset-3">
                                    <button class="btn btn-primary" ng-click="generateToken()" ng-disabled="disabled">
                                        <i class="fa fa-btn fa-key" ng-if=" ! disabled"></i>
                                        <i class="fa fa-btn fa-spinner fa-spin" ng-if="disabled"></i>
                                        Получить код подтверждения
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div ng-show="tokenRequested" ng-cloak>

                        <form class="form-horizontal">

                            <strong>Код подтверждения отправлен на номер {{ phone }}</strong>

                            <p>Введите полученный код в поле ниже</p>

                            <div class="form-group">
                                <?= Html::label('Код подтверждения', null, ['class' => 'col-md-3 control-label']) ?>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" ng-model="token"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-offset-3">
                                    <button class="btn btn-primary" ng-click="validateToken()" ng-disabled="disabled">
                                        <i class="fa fa-btn fa-sign-in" ng-if=" ! disabled"></i>
                                        <i class="fa fa-btn fa-spinner fa-spin" ng-if="disabled"></i>
                                        <?= $validateCodeText ?>
                                    </button>

                                    <button class="btn btn-default" ng-click="reenterPhone()" ng-disabled="disabled">
                                        <i class="fa fa-btn fa-repeat" ng-if=" ! disabled"></i>
                                        <i class="fa fa-btn fa-spinner fa-spin" ng-if="disabled"></i>
                                        Запросить код еще раз
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>