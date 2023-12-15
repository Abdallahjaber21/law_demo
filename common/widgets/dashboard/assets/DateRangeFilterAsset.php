<?php

namespace common\widgets\dashboard\assets;

use yii\web\AssetBundle;

/**
 * Description of DateRangeFilterAsset
 *
 * @author Tarek K. Ajaj
 */
class DateRangeFilterAsset extends AssetBundle {

  public $sourcePath = '@bower';
  public $css = [
      'bootstrap-daterangepicker/daterangepicker.css',
  ];
  public $js = [
      'moment/moment.js',
      'bootstrap-daterangepicker/daterangepicker.js',
  ];
  public $depends = [
        'common\assets\BackendAsset'
  ];

}
