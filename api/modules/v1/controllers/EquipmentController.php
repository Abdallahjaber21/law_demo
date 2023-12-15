<?php


namespace api\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\models\Equipment;
use common\models\Location;
use common\models\User;

class EquipmentController extends ApiController
{

    public function actionAll()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getEquipments()->where(['status'=>Equipment::STATUS_ENABLED])->all();
    }

    public function actionGet($location_id)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $location = $user->getLocations()
            ->where(['id' => $location_id])
            ->andWhere(['status'=>Location::STATUS_ENABLED])
            ->one();

        if(!empty($location)){
            return $location->equipments;
        }
        return [];
    }

}