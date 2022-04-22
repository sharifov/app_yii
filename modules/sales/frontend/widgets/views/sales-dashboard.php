<?php
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 */

\yz\icons\FontAwesomeAsset::register($this);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-success">
                <div class="panel-heading">
                    Совершенные продажи продукции
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a class="btn btn-primary" href="<?= Url::to(['/sales/sales/app']) ?>">
                                <i class="fa fa-plus"></i>
                                Оформить новую продажу
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a class="btn" href="<?= Url::to(['/sales/sales/index']) ?>">Список оформленных продаж</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>