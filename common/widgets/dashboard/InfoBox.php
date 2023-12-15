<?php

namespace common\widgets\dashboard;

use yii\base\Widget;

/**
 * Description of InfoBox
 *
 * @author Tarek K. Ajaj
 * May 3, 2017 2:38:39 PM
 * 
 * InfoBox.php
 * UTF-8
 * 
 */
class InfoBox extends Widget {


    public $title;
    public $icon = 'info';
    public $color = "gray";
    public $iconcolor;
    public $number = 0;
    public $size = 30;

    public function init() {
        parent::init();
                
    }

    public function run() {
        return $this->render("info-box", [
                    'color' => $this->color,
                    'icon' => $this->icon,
                    'title' => $this->title,
                    'number' => $this->number,
                    'size' => $this->size,
                    'iconcolor' => $this->iconcolor,
        ]);
    }

}
