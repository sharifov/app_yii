<?php

namespace  modules\sales\backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


/**
 * Class FancyBoxAsset
 */
class FancyBoxAsset extends AssetBundle
{
    public $sourcePath = '@bower/fancybox/source';

    public $js = [
        'jquery.fancybox.pack.js',
    ];
    public $css = [
        'jquery.fancybox.css',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
} 