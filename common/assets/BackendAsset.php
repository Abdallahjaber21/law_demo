<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend applications asset bundle.
 */
class BackendAsset extends AssetBundle
{
    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
        'scss/backend.css?_=1',
        'css/backend.css?_=1',
        'plugins/toastr/toastr.css',
        '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic'
    ];
    public $js = [
        'js/backend.js',
        'plugins/toastr/toastr.min.js',
        'js/screenfull/screenfull.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
