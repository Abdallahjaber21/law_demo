<?php

namespace common\components\notification\widgets;

use yii\base\Widget;

/**
 * Description of NotificationScripts
 *
 * @author Tarek K. Ajaj
 */
class NotificationScripts extends Widget {

    public function run() {
        return $this->render("notification-scripts");
    }

}
