<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\components\extensions;

use Yii;
use yii\debug\Module;
use yii\web\View;

/**
 * Description of CrossAppDebug
 *
 * @author Tarek K. Ajaj
 */
class CrossAppDebug extends Module {

  public function init() {
    $this->setViewPath("@vendor/yiisoft/yii2-debug/src/views");
    parent::init();
  }

  public function beforeAction($action) {
    Yii::$app->getView()->off(View::EVENT_END_BODY, [Yii::$app->modules['debug'], 'renderToolbar']);
    return parent::beforeAction($action);
  }

}
