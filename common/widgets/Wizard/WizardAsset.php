<?php

namespace common\widgets\Wizard;

use yii\web\AssetBundle;

/**
 * Description of WizardAsset
 *
 * @author Tarek K. Ajaj
 */
class WizardAsset extends AssetBundle {

  public function init() {
    parent::init();
    \Yii::$app->assetManager->forceCopy = true;
  }

  public $sourcePath = '@common/widgets/Wizard/assets';
  public $css = [
      'css/gsdk-bootstrap-wizard.css',
      'css/extra.css',
  ];
  public $js = [
      'js/jquery.bootstrap.wizard.js',
      'js/gsdk-bootstrap-wizard.js',
      'js/jquery.validate.min.js',
  ];
  public $depends = [
      'yii\web\YiiAsset',
      'yii\bootstrap\BootstrapAsset',
  ];

}
