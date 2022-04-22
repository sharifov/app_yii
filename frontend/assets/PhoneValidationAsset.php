<?php

namespace frontend\assets;

use marketingsolutions\assets\AngularAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\widgets\MaskedInputAsset;
use yz\icons\FontAwesomeAsset;


/**
 * Class PhoneValidationAsset
 */
class PhoneValidationAsset extends AssetBundle
{
    public $sourcePath = '@frontend/assets/phone-validation';

    public $js = [
        'app.js',
    ];

    public $depends = [
        YiiAsset::class,
        AngularAsset::class,
        FontAwesomeAsset::class,
        MaskedInputAsset::class
    ];
}

