<?php

namespace common\assets\plugins;

use yii\web\AssetBundle;

/**
 * Description of Fancybox3Asset
 *
 * @author Tarek K. Ajaj
 * Feb 3, 2017 8:37:28 AM
 * 
 * Fancybox3Asset.php
 * UTF-8
 * 
 */
class Fancybox3Asset extends AssetBundle {

    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
        'plugins/fancybox3/jquery.fancybox.min.css',
    ];
    public $js = [
        'plugins/fancybox3/jquery.fancybox.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
