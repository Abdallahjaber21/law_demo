<?php

namespace common\widgets\inputs;

use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Description of ICheck
 *
 * @author Tarek K. Ajaj
 * Apr 28, 2017 11:14:51 AM
 * 
 * ICheck.php
 * UTF-8
 * 
 */
class ICheck extends InputWidget {

    public $color = 'blue';

    public function init() {
        parent::init();
        ICheckAsset::register($this->view);
    }

    public function run() {
        $this->view->registerJs("$('.icheck').iCheck({" .
                "checkboxClass: 'icheckbox_square-{$this->color}'," .
                "radioClass: 'icheckbox_square-{$this->color}'," .
                "increaseArea: '20%'" .
                "});");

        if (empty($this->options['class'])) {
            $this->options['class'] = "";
        }
        $this->options['class'] = $this->options['class'] . " icheck";
        if (empty($this->name)) {
            return Html::activeCheckbox($this->model, $this->attribute, $this->options);
        } else {
            return Html::checkbox($this->name, $this->value, $this->options);
        }
    }

}
