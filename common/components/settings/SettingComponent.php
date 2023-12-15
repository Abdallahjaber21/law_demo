<?php

namespace common\components\settings;

use yii\base\Component;

/**
 * SettingComponent
 *
 * @author Tarek K. Ajaj
 * Apr 7, 2017 11:29:48 AM
 * 
 * SettingComponent.php
 * UTF-8
 * 
 */
class SettingComponent extends Component {

    /**
     * Settings Configuration Array to be loaded 
     * @var array 
     */
    public $settings;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    /**
     * Get the original settings configuration loaded from the file
     * 
     * @return array
     */
    public function getConfiguration() {
        return $this->settings;
    }

    /**
     * get the value of a given setting
     * 
     * @param string $name
     * @return mixed
     */
    public function getValue($name) {
        return Setting::getValue($name);
    }

}
