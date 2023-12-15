<?php

namespace common\components\notification;

use common\models\Technician;
use common\models\User;
use common\models\users\AbstractAccount;
use common\models\users\Account;
use common\models\users\Admin;
use common\models\users\AgencyManager;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\file\Queue;
use yii\queue\JobInterface;

/**
 * Description of NotificationJob
 *
 * @author Tarek K. Ajaj
 */
class NotificationJob extends BaseObject implements JobInterface
{

    public $notification_id;
    private $click_action;

    /**
     *
     * @param Queue $queue
     */
    public function execute($queue)
    {
        echo "################ NotifyUser Job Started ############################\n";

        /* @var $notification Notification */
        $notification = Notification::find()
            ->where(['id' => $this->notification_id])
            ->one();
        if (!empty($notification)) {
            $userObject = $this->getUserObject($notification->account);
            echo " --- Account ID     : " . $notification->account_id . " --- \n";
            echo " --- Notification ID: " . $this->notification_id . " --- \n";
            echo " --- URL            : " . $notification->url . " --- \n";
            if (!empty($userObject)) {
                $regids = array_filter([$userObject->web_registration_id, $userObject->mobile_registration_id]);
                if ($userObject->enable_notification
                    && !empty($regids)
                    && $this->isNotificationEnabled($notification, $notification->account, $userObject)) {
                    $message = NotificationMessages::getMessage($notification->message, Json::decode($notification->params), $userObject->language);

                    echo " --- Mobile RegID   : " . $userObject->mobile_registration_id . " --- \n";
                    echo " --- Web RegID      : " . $userObject->web_registration_id . " --- \n";
                    echo " --- Language       : " . $userObject->language . " --- \n";
                    echo " --- Message        : " . $message . " --- \n";
                    $this->clickAction($notification, $notification->account);
                    if (!empty($userObject->mobile_registration_id)) {
                        $platform = !empty($userObject->platform)? $userObject->platform : 'web';
                        $this->sendNotification($notification, [$userObject->mobile_registration_id], $message, $platform);
                    }
                    if (!empty($userObject->web_registration_id)) {
                        $this->sendNotification($notification, [$userObject->web_registration_id], $message, "web");
                    }
                } else {
                    echo " xxx Regids not found or notificaiton not enabled xxx";
                }
            } else {
                echo " xxx Account not found xxx";
            }
        } else {
            echo " xxx Notification not found xxx";
        }
        echo "\n";
        echo "################ NotifyUser Job Ended ############################\n";
        echo "\n\n\n";
    }

    /**
     *
     * @param Account $account
     * @return AbstractAccount
     */
    private function getUserObject($account)
    {
        switch ($account->type) {
            case 'admin':
                return Admin::findOne(['account_id' => $account->id]);
            case 'user':
                return User::findOne(['account_id' => $account->id]);
            case 'technician':
                return Technician::findOne(['account_id' => $account->id]);
        }
        return FALSE;
    }

    /**
     *
     * @param Notification $notification
     * @param Account $account
     * @return AbstractAccount
     */
    private function isNotificationEnabled($notification, $account, $userObject)
    {
        switch ($account->type) {
            case 'admin':
                return true;
            case 'user':
                /* @var $account User */
                if($notification->type == Notification::TYPE_NEWS && $userObject->news_notifications){
                    return true;
                }
                if($notification->type == Notification::TYPE_CONTRACT && $userObject->contracts_reminders){
                    return true;
                }
                if($notification->type == Notification::TYPE_SERVICE && $userObject->maintenance_notifications){
                    return true;
                }

                if($notification->type == Notification::TYPE_NOTIFICATION || $notification->type == Notification::TYPE_WARNING){
                    return true;
                }
                return false;
            case 'technician':
                return true;
        }
        return true;
    }

    /**
     *
     * @param Notification $notification
     * @param Account $account
     */
    private function clickAction($notification, $account)
    {
        $url = !empty($notification->url) ? Json::decode($notification->url) : ['/site/index'];
        $url['language'] = $this->getUserObject($account)->language;
        //$this->click_action = \Yii::$app->urlManagerAdmin->createAbsoluteUrl($url);
        echo " --- Account Type   : " . $account->type . " --- \n";
        switch ($account->type) {
            case 'admin':
                $this->click_action = \Yii::$app->urlManagerAdmin->createAbsoluteUrl($url);
                break;
            default:
                $this->click_action = \Yii::$app->urlManagerAdmin->createAbsoluteUrl($url);
                break;
        }
        echo " --- Click Action   : " . Json::encode($this->click_action) . " --- \n";
    }

    /**
     *
     * @param Notification $notification
     * @param string $regids
     */
    function sendNotification($notification, $regids, $message, $platform = 'web')
    {
        if (empty($regids)) {
            return;
        }
        $API_ACCESS_KEY = Yii::$app->params['pushNotificationKey'];
        $registrationIds = $regids;

        //#prep the bundle
        $msg = [
            'body' => $message,
            'title' => $notification->title,//Yii::$app->params['project-name'],
            "notId" => time(),
            //"sound"=> "default",
            'click_action' => $this->click_action,
            //'mobile_action' => Json::decode($notification->mobile_action)
            //'icon' => Yii::getAlias("@staticWeb") . "/images/logo-small.png"
        ];
        $fields = [
            'registration_ids' => $registrationIds,
            'notification' => $msg,
            'data' => $msg,
            'alert' => [
                'title' => Yii::$app->params['project-name'],
                'body' => $message,
            ],
        ];
//        if ($platform == 'web') {
//            $fields = [
//                'registration_ids' => $registrationIds,
//                'notification' => $msg
//            ];
//        }
        if ($platform == 'android') {
            $fields = [
                'registration_ids' => $registrationIds,
                'data' => $msg,
                'alert' => [
                    'title' => Yii::$app->params['project-name'],
                    'body' => $message,
                ],
            ];
        }
        if ($platform == 'ios') {
            $fields = [
                'content_available' => true,
                'mutable_content' => true,
                'registration_ids' => $registrationIds,
                'data' => $msg,
                'notification' => $msg,
            ];
        }

        echo " --- FIELDS         : " . Json::encode($fields) . " --- \n";
        $headers = [
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        ];
        //#Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //#Echo Result Of FireBase Server
        echo $result;
    }

}
