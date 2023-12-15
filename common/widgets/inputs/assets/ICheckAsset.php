<?php

namespace common\widgets\inputs\assets;

use yii\web\AssetBundle;

/**
 * Description of ICheckAsset
 *
 * @author Tarek K. Ajaj
 * Apr 28, 2017 11:15:25 AM
 * 
 * ICheckAsset.php
 * UTF-8
 * 
 */
class ICheckAsset extends AssetBundle
{

    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/iCheck';
    public $css = [
        'square/_all.css'
    ];
    public $js = [
        'icheck.min.js'
    ];
    public $depends = [
        //'dmstr\web\AdminLteAsset',
    ];
}
