<?php

namespace common\widgets\inputs;

use yii\base\Widget;

/**
 * Description of LocationPicker
 *
 * @author Tarek K. Ajaj
 * May 2, 2017 4:52:54 PM
 * 
 * LocationPicker.php
 * UTF-8
 * 
 */
class LocationPicker extends Widget
{

    public $id;
    public $address_attr;
    public $latitude_attr;
    public $longitude_attr;
    public $latitude;
    public $longitude;
    public $view_only = false;

    public function init()
    {
        parent::init();
        if (empty($this->id)) {
            $this->id = $this->getId();
        }
    }

    public function run()
    {
        return $this->render('location-picker', [
            'id' => $this->id,
            'address_attr' => $this->address_attr,
            'latitude_attr' => $this->latitude_attr,
            'longitude_attr' => $this->longitude_attr,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'view_only' => $this->view_only
        ]);
    }
}
