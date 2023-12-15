<?php

namespace common\widgets\inputs\assets;

use yii\web\AssetBundle;

/**
 * Description of IntlTelInputAsset
 *
 * @author Tarek K. Ajaj
 */
class IntlTelInputAsset extends AssetBundle {

    public $basePath = '@static';
    public $baseUrl = '@staticWeb';
    public $css = [
        'plugins/intl-tel-input/css/intlTelInput.css'
    ];
    public $js = [
        'plugins/intl-tel-input/js/intlTelInput.min.js'
    ];
    public $depends = [
    ];

}
