<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Description of BowerAsset
 *
 * @author Tarek K. Ajaj
 */
class BowerAsset extends AssetBundle {

  public $sourcePath = '@bower';
  public $css = [
  ];
  public $js = [
      'jquery-slimscroll/jquery.slimscroll.min.js',
  ];
  public $depends = [
        'common\assets\BackendAsset'
  ];

}
