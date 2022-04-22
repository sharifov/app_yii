<?php

namespace modules\sales\common\app\assets;

use marketingsolutions\assets\AngularAsset;
use yii\web\AssetBundle;
use yz\icons\FontAwesomeAsset;

class SaleApp2Asset extends AssetBundle
{
    public $sourcePath = '@modules/sales/common/app/assets/sale-app2';

    public $js = [
        'js/app.js',
    ];

    public $depends = [
        AngularAsset::class,
        FontAwesomeAsset::class,
        NgFileUploadAsset::class,
    ];
}