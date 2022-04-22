<?php

namespace modules\sales\common\app\assets;

use marketingsolutions\assets\AngularAsset;
use yii\web\AssetBundle;
use yz\icons\FontAwesomeAsset;


/**
 * Class SaleAppAsset
 */
class SaleAppAsset extends AssetBundle
{
    public $sourcePath = '@modules/sales/common/app/assets/sale-app';

    public $js = [
        'js/app.js',
    ];

    public $depends = [
        AngularAsset::class,
        FontAwesomeAsset::class,
        NgFileUploadAsset::class,
    ];
}