<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Description of SwiperAssets
 *
 * @author Tarek K. Ajaj
 */
class SwiperAssets extends AssetBundle {

  public $basePath = '@static';
  public $baseUrl = '@staticWeb';
  public $css = [
      'plugins/swiper-4.4.6/css/swiper.min.css',
  ];
  public $js = [
      'plugins/swiper-4.4.6/js/swiper.min.js',
  ];
  public $depends = [
      'yii\web\YiiAsset',
      'yii\bootstrap\BootstrapAsset',
  ];

}
