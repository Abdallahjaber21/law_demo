<?php

namespace common\components\extensions;

use dosamigos\google\maps\Event;
use edofre\markerclusterer\Marker;
use yii\helpers\ArrayHelper;

/**
 * Description of MarkerWithLabel
 *
 * @author Tarek K. Ajaj
 */
class MarkerWithLabel extends Marker {

  /**
   * @inheritdoc
   *
   * @param array $config
   */
  public function __construct($config = []) {

    $this->options = ArrayHelper::merge(
                    [
                'labelContent' => null,
                'labelAnchor' => null,
                'labelClass' => null,
                'labelStyle' => null,
                    ], $this->options
    );

    parent::__construct($config);
  }

  /**
   * The constructor js code for the Marker object
   * @return string
   */
  public function getJs() {
    $js = $this->getInfoWindowJs();

    //$js[] = "var {$this->getName()} = new google.maps.Marker({$this->getEncodedOptions()});";
    $js[] = "var {$this->getName()} = new MarkerWithLabel({$this->getEncodedOptions()});";
    // add the marker to markers array
    $js[] = "markers.push({$this->getName()});";

    foreach ($this->events as $event) {
      /** @var Event $event */
      $js[] = $event->getJs($this->getName());
    }

    return implode("\n", $js);
  }

}
