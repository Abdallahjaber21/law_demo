<?php


namespace common\widgets\inputs\assets;


use yii\web\AssetBundle;


class PinCodeAsset extends AssetBundle
{

    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
        'plugins/jquery-pin-code/css/bootstrap-pincode-input.css'
    ];
    public $js = [
        'plugins/jquery-pin-code/js/bootstrap-pincode-input.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

}