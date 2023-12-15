<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Description of ChartJsAsset
 *
 * @author Tarek K. Ajaj
 */
class TimePickerAsset extends AssetBundle {

  public $sourcePath = '@npm';
  public $css = [
      'jquery-timepicker/jquery.timepicker.css',
  ];
  public $js = [
      'jquery-timepicker/jquery.timepicker.js',
  ];
  public $depends = [
  ];

}
