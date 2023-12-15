<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Description of ChartJsAsset
 *
 * @author Tarek K. Ajaj
 */
class ChartJsAsset extends AssetBundle {

  public $sourcePath = '@npm';
  public $css = [
  ];
  public $js = [
      'chart.js/dist/chart.min.js',
      'chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.js',
      'chartjs-plugin-annotation/dist/chartjs-plugin-annotation.js',
  ];
  public $depends = [
  ];

}
