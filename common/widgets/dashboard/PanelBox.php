<?php

namespace common\widgets\dashboard;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Description of PanelBox
 *
 * @author Tarek K. Ajaj
 * Apr 10, 2017 9:38:07 AM
 * 
 * PanelBox.php
 * UTF-8
 * 
 */
class PanelBox extends Widget {

    CONST COLOR_GRAY = "default";
    CONST COLOR_GREEN = "success";
    CONST COLOR_RED = "danger";
    CONST COLOR_BLUE = "primary";
    CONST COLOR_LIGHTBLUE = "info";
    CONST COLOR_ORANGE = "warning";
    CONST COLOR_OLIVE = "olive";
    CONST COLOR_PURPLE = "purple";
    CONST COLOR_NAVY = "navy";
    CONST COLOR_TEAL = "teal";

    public $title;
    public $icon;
    public $color;
    public $bgColor;
    public $buttons;
    public $headerItems;
    public $footer;
    public $body = true;
    public $header = true;
    public $withBorder = true;
    public $solid = false;
    public $panelClass;
    public $footerClass;
    public $canMinimize = false;
    public $canClose = false;
    public $help = false;

    public function init() {
        parent::init();
        $this->color = !empty($this->color) ? $this->color : self::COLOR_GRAY;
        $this->buttons = [];
        $this->headerItems = [];
        ob_start();
    }

    public function run() {
        $content = ob_get_clean();
        return $this->render("panel-box", [
                    'id' => "box-".$this->getId(),
                    'content' => $content,
                    'color' => $this->color,
                    'icon' => $this->icon,
                    'title' => $this->title,
                    'buttons' => $this->buttons,
                    'headerItems' => $this->headerItems,
                    'footer' => $this->footer,
                    'body' => $this->body,
                    'header' => $this->header,
                    'solid' => $this->solid,
                    'withBorder' => $this->withBorder,
                    'bgColor' => $this->bgColor,
                    'panelClass' => $this->panelClass,
                    'footerClass' => $this->footerClass,
                    'canMinimize' => $this->canMinimize,
                    'canClose' => $this->canClose,
                    'help' => $this->help,
        ]);
    }

    /**
     * adds a button to the list of buttons in panel box tool box
     * 
     * @param string $text
     * @param string|array $url
     * @param array $options
     */
    public function addButton($text, $url = null, $options = []) {
        if (!empty($options['class'])) {
            //add btn & btn-sm class to the button class list
            $options['class'] = array_unique(explode(" ", "{$options['class']} btn btn-sm"));
        } else {
            $options['class'] = 'btn btn-sm btn-info';
        }
        $this->buttons[] = Html::a($text, $url, $options);
    }

    /**
     * start collecting footer code
     */
    public function beginHeaderItem() {
        ob_start();
    }

    /**
     * end footer and set as variable
     */
    public function endHeaderItem() {
        $this->headerItems[] = ob_get_clean();
    }

    /**
     * start collecting footer code
     */
    public function beginFooter() {
        ob_start();
    }

    /**
     * end footer and set as variable
     */
    public function endFooter() {
        $this->footer = ob_get_clean();
    }

    /**
     * sets the footer of the panelbox
     * 
     * @param mixed $footer
     */
    public function setFooter($footer) {
        $this->footer = $footer;
    }

}
