<?php

namespace common\components\notification\widgets;

use common\components\notification\Notification;
use Yii;
use yii\base\Widget;

/**
 * Description of NotificationsPanelWidget
 *
 * @author Tarek K. Ajaj
 */
class NotificationsPanelWidget extends Widget {

    private $notifications;
    private $unseen;

    public function init() {
        $userId = Yii::$app->getUser()->getId();
        $this->notifications = Notification::find()
                ->where(['account_id' => $userId])
                ->limit(20)
                ->orderBy(['id' => SORT_DESC])
                ->all();
        $this->unseen = Notification::find()
                ->where([
                    'AND',
                    ['account_id' => $userId],
                    ['seen' => false],
                ])
                ->count();
    }

    public function run() {
        return $this->render("notifications-panel", [
                    'notifications' => $this->notifications,
                    'unseen' => $this->unseen
        ]);
    }

}
