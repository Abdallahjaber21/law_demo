<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend applications asset bundle.
 */
class DropZoneAsset extends AssetBundle
{
    public $basePath = '@vendor';

    public $baseUrl = '@vendorWeb';

    public $css = [
        'enyo/dropzone/dist/min/dropzone.min.css',
    ];
    public $js = [
        'enyo/dropzone/dist/min/dropzone.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
