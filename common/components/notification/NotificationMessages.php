<?php

namespace common\components\notification;

/**
 * Description of NotificationMessages
 *
 * @author Tarek K. Ajaj
 */
class NotificationMessages
{
    //
    CONST ADMIN__NEW_USER_REGISTERED = "ADMIN:NEW_USER_REGISTERED";

    public static function getMessage($messageId, $params = [], $language = null)
    {
        switch ($messageId) {
            case self::ADMIN__NEW_USER_REGISTERED:
                return \Yii::t("notification", "New user registered: {name}", $params, $language);
            default:
                return $messageId;
        }
    }

}
