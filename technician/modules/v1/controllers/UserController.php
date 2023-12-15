<?php

namespace technician\modules\v1\controllers;

use common\models\Technician;
use common\models\TechnicianLocation;
use common\models\User;
use Yii;

/**
 * Description of UserController
 *
 * @author Tarek K. Ajaj
 */
class UserController extends \common\components\extensions\api\UserController
{

    public function actionNotification()
    {
        $this->isPost();
        /* @var $user User */
        $user = Yii::$app->getUser()->getIdentity();
        $user->enable_notification = Yii::$app->request->post("enable_notification");
        $user->save(false);
        return ['success' => true];
    }

    public function actionLocation()
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        TechnicianLocation::deleteAll(['technician_id' => $user->id]);

        $lat = \Yii::$app->getRequest()->post("latitude");
        $lng = \Yii::$app->getRequest()->post("longitude");
        (new TechnicianLocation([
            'latitude'      => $lat,
            'longitude'     => $lng,
            'technician_id' => $user->id
        ]))->save();

        return [
            'success' => true
        ];
    }

    public function actionLogout()
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();
        $user->mobile_registration_id = null;
        $user->save(false);
        return [
            'success' => true
        ];
    }
}
