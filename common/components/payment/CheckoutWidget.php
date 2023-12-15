<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\components\payment;

use yii\base\Widget;

/**
 * Description of CheckoutWidget
 *
 * @author Tarek K. Ajaj
 */
class CheckoutWidget extends Widget {

  public $sessionId;

  public function init() {
    parent::init();
  }

  public function run() {
    return $this->render("checkout", [
                'sessionId' => $this->sessionId
    ]);
  }

}
