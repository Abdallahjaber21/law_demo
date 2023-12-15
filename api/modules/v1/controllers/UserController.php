<?php

namespace api\modules\v1\controllers;

use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use common\models\Metric;
use common\models\Metrics;
use common\models\User;
use common\models\users\AbstractAccount;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * Description of UserController
 *
 * @author Tarek K. Ajaj
 */
class UserController extends \common\components\extensions\api\UserController
{

    public function actionNotification() {
        $this->isPost();
        /* @var $user User */
        $user = Yii::$app->getUser()->getIdentity();
        $user->enable_notification = Yii::$app->request->post("enable_notification");
        $user->contracts_reminders = Yii::$app->request->post("contracts_reminders");
        $user->maintenance_notifications = Yii::$app->request->post("maintenance_notifications");
        $user->news_notifications = Yii::$app->request->post("news_notifications");
        $user->save(false);
        AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
        $user->refresh();
        return $user;
    }

    public function actionLogout()
    {
        /* @var $user User */
        $user = Yii::$app->getUser()->getIdentity();
        $user->mobile_registration_id = null;
        $user->web_registration_id = null;
        $user->save(false);
        return ['success'=>true];
    }

}
