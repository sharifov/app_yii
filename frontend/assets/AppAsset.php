<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        '/js/my.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yz\assets\YzAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
		'yz\icons\FontAwesomeAsset',
    ];
}