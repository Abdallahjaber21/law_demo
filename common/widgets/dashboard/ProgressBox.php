<?php


namespace common\widgets\dashboard;

use yii\bootstrap\Widget;

/**
 * Description of ProgressBox
 *
 * @author Tarek K. Ajaj
 * Jun 12, 2017 8:32:31 AM
 *
 * ProgressBox.php
 * UTF-8
 *
 */
class ProgressBox extends Widget
{

    public $title;
    public $icon = 'info';
    public $color = "gray";
    public $number = 0;
    public $percentage = 50;
    public $description = '';
    public $button = '';
    public $format = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render("progress-box", [
            'color' => $this->color,
            'icon' => $this->icon,
            'title' => $this->title,
            'number' => $this->number,
            'description' => $this->description,
            'percentage' => $this->percentage,
            'button' => $this->button,
        ]);
    }

}
