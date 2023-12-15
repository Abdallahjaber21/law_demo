<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend applications asset bundle.
 */
class LoginAsset extends AssetBundle {

  public $basePath = '@static';
  public $baseUrl = '@staticWeb';
  public $css = [
      'css/login.css',
  ];
  public $js = [
  ];
  public $depends = [
  ];

}
