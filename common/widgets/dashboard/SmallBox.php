<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\widgets\dashboard;

use yii\bootstrap\Widget;

/**
 * Description of SmallBox
 *
 * @author Tarek K. Ajaj
 * Jun 12, 2017 8:32:31 AM
 * 
 * SmallBox.php
 * UTF-8
 * 
 */
class SmallBox extends Widget {

    public $title;
    public $icon = 'info';
    public $color = "gray";
    public $number = 0;
    public $link = '#';
    public $linkLabel = 'More Info';

    public function init() {
        parent::init();
    }

    public function run() {
        return $this->render("small-box", [
                    'color' => $this->color,
                    'icon' => $this->icon,
                    'title' => $this->title,
                    'number' => $this->number,
                    'link' => $this->link,
                    'linkLabel' => $this->linkLabel,
        ]);
    }

}
