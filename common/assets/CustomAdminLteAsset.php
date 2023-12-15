<?php


namespace common\assets;


use yii\web\AssetBundle as BaseAdminLteAsset;
use yii\web\AssetBundle;

class CustomAdminLteAsset extends AssetBundle
{
    public $basePath = '@static';
    public $baseUrl = '@staticWeb';

    public $css = [
        'plugins/override/AdminLTE.css',
    ];


    public $depends = [
        'rmrevin\yii\fontawesome\AssetBundle',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}