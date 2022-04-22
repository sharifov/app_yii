<?php

namespace modules\sales\common\app\assets;

use marketingsolutions\assets\AngularAsset;
use yii\web\AssetBundle;
use yz\icons\FontAwesomeAsset;


/**
 * Class SaleGoldAsset
 */
class SaleGoldAsset extends AssetBundle
{
    public $sourcePath = '@modules/sales/common/app/assets/sale-gold';

    public $js = [
        'js/app.js',
    ];

    public $depends = [
        AngularAsset::class,
        FontAwesomeAsset::class,
        NgFileUploadAsset::class,
    ];
}