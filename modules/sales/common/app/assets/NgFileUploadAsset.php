<?php

namespace modules\sales\common\app\assets;

use yii\web\AssetBundle;


/**
 * Class NgFileUploadAsset
 */
class NgFileUploadAsset extends AssetBundle
{
    public $sourcePath = '@bower/ng-file-upload';

    public $js = [
        'ng-file-upload-all.min.js',
    ];
}