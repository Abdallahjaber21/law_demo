<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main rtl backend applications asset bundle.
 */
class BackendRTLAsset extends AssetBundle
{
    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
        'rtl/admin-lte/AdminLTE.rtl.min.css',
        'rtl/bootstrap/bootstrap.rtl.min.css',
        'css/backend-rtl.css',
    ];
    public $js = [
    ];
    public $depends = [
        'common\assets\BackendAsset'
    ];
}
