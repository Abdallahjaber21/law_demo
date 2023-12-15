<?php

namespace common\assets\plugins;

use yii\web\AssetBundle;

/**
 * Description of RepeaterAsset
 *
 * @author Tarek K. Ajaj
 * Feb 23, 2017 1:54:41 PM
 * 
 * RepeaterAsset.php
 * UTF-8
 * 
 */
class RepeaterAsset extends AssetBundle {

    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
    ];
    public $js = [
        'plugins/repeater/jquery.repeater.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
