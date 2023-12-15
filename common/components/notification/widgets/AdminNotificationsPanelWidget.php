<?php

namespace common\components\notification\widgets;

use common\components\notification\Notification;
use common\models\AdminNotifications;
use Yii;
use yii\base\Widget;

/**
 * Description of NotificationsPanelWidget
 *
 * @author Tarek K. Ajaj
 */
class AdminNotificationsPanelWidget extends Widget
{
    public $notifications;
    public $unseen;

    public function init()
    {
        $userId = Yii::$app->getUser()->getId();
        $this->notifications = AdminNotifications::find()
            // ->where(['technician_id' => $userId])
            ->where([
                'AND',
                // ['technician_id' => $userId],
                ['seen' => false],
            ])
            ->limit(20)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $this->unseen = AdminNotifications::find()
            ->where([
                'AND',
                // ['technician_id' => $userId],
                ['seen' => false],
            ])
            ->count();
    }

    public function run()
    {
        return $this->render("admin-notifications-panel", [
            'notifications' => $this->notifications,
            'unseen' => $this->unseen
        ]);
    }

}
