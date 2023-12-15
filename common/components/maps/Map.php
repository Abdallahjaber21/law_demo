<?php
namespace common\components\maps;

use yii\base\Widget;

class Map extends Widget
{
    public $features;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function run()
    {
        return $this->render("map", [
            'id' => $this->getId(),
            'features' => $this->features,
        ]);
    }

}