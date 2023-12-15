<?php

namespace common\widgets\Wizard;

use yii\base\Widget;

/**
 * Description of Wizard
 *
 * @author Tarek K. Ajaj
 */
class Wizard extends Widget {

  public $options;
  public $tabs;

  public function init() {
    parent::init();
    WizardAsset::register($this->view);
  }

  
  public function run() {
    return $this->render("wizard", [
                'options' => $this->options,
                'tabs' => $this->tabs,
    ]);
  }

}
