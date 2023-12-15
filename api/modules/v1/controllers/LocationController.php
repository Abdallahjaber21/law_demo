<?php


namespace api\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\models\DefaultLocation;
use common\models\Location;
use common\models\LocationCode;
use common\models\Maintenance;
use common\models\MaintenanceReport;
use common\models\Problem;
use common\models\RemovalRequest;
use common\models\RepairRequest;
use common\models\User;
use common\models\UserLocation;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class LocationController extends ApiController
{

    public function actionAdd()
    {
        $this->isPost();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $location_code = \Yii::$app->request->post("location_code");

        $locationCode = LocationCode::find()
            ->where([
                'AND',
                ['code' => $location_code],
                //['status' => LocationCode::STATUS_ENABLED]
            ])
            ->one();

        if (!empty($locationCode)) {
            if ($locationCode->usages_limit > $locationCode->usages_count) {
                /* @var $userLocation UserLocation */
                $userLocation = UserLocation::find()->where([
                    'AND',
                    ['location_id' => $locationCode->location_id],
                    ['user_id' => $user->id]
                ])
                    ->one();
                if (!empty($userLocation)) {
                    if ($locationCode->type == LocationCode::TYPE_DECISION_MAKER) {
                        $userLocation->role = UserLocation::ROLE_DECISION_MAKER;
                        $userLocation->save(false);
                    }
                } else {
                    $userLocation = new UserLocation();

                    if ($locationCode->type == LocationCode::TYPE_DECISION_MAKER) {
                        $userLocation->role = UserLocation::ROLE_DECISION_MAKER;
                    }
                    if ($locationCode->type == LocationCode::TYPE_RESIDENT) {
                        $userLocation->role = UserLocation::ROLE_RESIDENT;
                    }

                    $userLocation->location_id = $locationCode->location_id;
                    $userLocation->user_id = $user->id;
                    $userLocation->status = UserLocation::STATUS_ENABLED;
                    $userLocation->save();

                    $locationCode->usages_count += 1;
                    $locationCode->save(false);
                }
                return ['success' => true];
            }
            throw new ServerErrorHttpException("Location code limit exceeded");
        }
        throw new ServerErrorHttpException("Invalid location code");
    }

    public function actionGet()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getLocations()
            ->where(['status' => Location::STATUS_ENABLED])
            ->all();
        //return $user->getCustomers()->all();

    }

    public function actionGetReports()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $locationsIds = ArrayHelper::getColumn($user->getLocations()
            ->select(['id'])
            ->where(['status' => Location::STATUS_ENABLED])
            ->asArray()
            ->all(), 'id', false);

        return MaintenanceReport::find()
            ->with(['location'])
            ->where([
                'AND',
                ['location_id' => $locationsIds],
                ['>=', 'created_at', date("Y-m-d H:i:s", strtotime("-40 days"))]
            ])
            ->all();
        //            ->createCommand()->rawSql;
        //return $user->getCustomers()->all();

    }

    public function actionAllReports()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $locationsIds = ArrayHelper::getColumn($user->getLocations()
            ->select(['id'])
            ->where(['status' => Location::STATUS_ENABLED])
            ->asArray()
            ->all(), 'id', false);


        $repairs = RepairRequest::find()
            ->joinWith(['equipment', 'equipment.location'])
            ->where([
                'AND',
                [RepairRequest::tableName() . '.type' => RepairRequest::TYPE_REQUEST],
                [RepairRequest::tableName() . '.status' => [RepairRequest::STATUS_COMPLETED, RepairRequest::STATUS_COMPLETED]],
                [Location::tableName() . '.id' => $locationsIds],
                ['>=', RepairRequest::tableName() . '.departed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            ->orderBy(['departed_at' => SORT_DESC])
            ->all();

        $works = RepairRequest::find()
            ->joinWith(['equipment', 'equipment.location'])
            ->where([
                'AND',
                [RepairRequest::tableName() . '.type' => RepairRequest::TYPE_SCHEDULED],
                [RepairRequest::tableName() . '.status' => [RepairRequest::STATUS_COMPLETED, RepairRequest::STATUS_COMPLETED]],
                [Location::tableName() . '.id' => $locationsIds],
                ['>=', RepairRequest::tableName() . '.departed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            ->orderBy(['departed_at' => SORT_DESC])
            ->all();

        $maintenances = Maintenance::find()
            ->with(['report', 'equipment', 'location'])
            ->joinWith(['equipment', 'equipment.location'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE],
                [Maintenance::tableName() . '.report_generated' => true],
                [Location::tableName() . '.id' => $locationsIds],
                ['>=', Maintenance::tableName() . '.completed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            //->indexBy("id")
            //            ->createCommand()->rawSql;
            //->limit(20)
            ->orderBy(['completed_at' => SORT_DESC])
            ->all();

        Maintenance::$return_fields = Maintenance::FIELDS_REPORT;
        RepairRequest::$return_fields = RepairRequest::FIELDS_REPORT;
        return [
            'maintenances' => $maintenances,
            'repairs'      => $repairs,
            'works'        => $works,
        ];
    }

    public function actionGetDefault()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $defaultLocation DefaultLocation */
        $defaultLocation = $user->getDefaultLocation()->one();
        if (empty($defaultLocation)) {
            $location = $user->getLocations()
                ->where(['status' => Location::STATUS_ENABLED])
                ->one();
            if (!empty($location)) {
                $defaultLocation = DefaultLocation::setDefaultLocation($user->id, $location->id);
                return ['location' => $defaultLocation->location];
            }
        } else {
            $countCustomer = $user->getCustomers()->where(['id' => $defaultLocation->location->customer_id])->count();
            if ($countCustomer == 0) {
                $location = $user->getLocations()
                    ->where(['status' => Location::STATUS_ENABLED])
                    ->one();
                if (!empty($location)) {
                    $defaultLocation = DefaultLocation::setDefaultLocation($user->id, $location->id);
                    return ['location' => $defaultLocation->location];
                }
            } else {
                return ['location' => $defaultLocation->location];
            }
        }
        return ['location' => null];
    }

    public function actionSetDefault()
    {
        $this->isPost();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $location_id = \Yii::$app->getRequest()->post("location_id");

        $location = $user->getLocations()
            ->where([
                'AND',
                ['status' => Location::STATUS_ENABLED],
                ['id' => $location_id]
            ])
            ->one();

        if (!empty($location)) {
            DefaultLocation::setDefaultLocation($user->id, $location->id);
        }

        return ['location' => $location];
    }

    public function actionRemove()
    {
        $this->isPost();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $location_id = \Yii::$app->getRequest()->post("location_id");

        UserLocation::deleteAll([
            'AND',
            ['user_id' => $user->id],
            ['location_id' => $location_id]
        ]);

        return ['success' => true];
    }

    public function actionRequestForm()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();
        $locationsModels = $user->getLocations()
            ->where(['status' => Location::STATUS_ENABLED])
            ->indexBy("id")
            ->all();
        $locations = [];
        foreach ($locationsModels as $location_id => $locationsModel) {
            $locations[$location_id] = $locationsModel->toArray();
        }

        $locationIds = array_keys($locations);
        $userLocations = UserLocation::find()
            ->select(['location_id', 'removed_units'])
            ->where([
                'AND',
                ['user_id' => $user->id],
                ['location_id' => $locationIds]
            ])
            ->asArray()
            ->all();
        $removedUnitsPerLocation = ArrayHelper::map($userLocations, "location_id", "removed_units");
        foreach ($removedUnitsPerLocation as $location_id => $removed_units) {
            if (!empty($removed_units)) {
                $unitsIds = explode(",", $removed_units);
                foreach ($unitsIds as $unitsId) {
                    unset($locations[$location_id]['units'][$unitsId]);
                }
            }
        }
        return [
            '$removedUnitsPerLocation' => $removedUnitsPerLocation,
            '$userLocations'           => $userLocations,
            'locations'                => $locations,
            'problems'                 => Problem::findEnabled()->indexBy("id")->all()
        ];
    }

    public function actionGetUsers($location_id)
    {
        $this->isGet();
        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['user_id' => $user->id],
                ['role' => UserLocation::ROLE_DECISION_MAKER],
            ])
            ->one();

        if (!empty($userLocation)) {
            return UserLocation::find()
                ->where([
                    'AND',
                    ['location_id' => $location_id],
                ])
                ->all();
        }
        return [];
    }

    public function actionGetRemovedUnits($location_id)
    {
        $this->isGet();
        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['user_id' => $user->id],
            ])
            ->one();

        if (!empty($userLocation)) {
            return [
                'is_locked'     => $userLocation->is_locked,
                'removed_units' => explode(",", $userLocation->removed_units)
            ];
        }
        return [];
    }

    public function actionRequestRemove()
    {
        $this->isPost();
        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $location_id = \Yii::$app->request->post("location_id");
        $id = \Yii::$app->request->post("id");
        $reason = \Yii::$app->request->post("reason");

        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['id' => $id],
            ])
            ->one();

        if (!empty($userLocation)) {
            //TODO send email to admin
            $removalRequest = new RemovalRequest();
            $removalRequest->requester_id = $user->id;
            $removalRequest->user_location_id = $userLocation->id;
            $removalRequest->reason = $reason;
            $removalRequest->save();
            \Yii::$app->mailer->compose('notification', [
                'data' => [
                    'title'   => "User removal request",
                    'message' => "`{$user->name}` requested the removal of `{$userLocation->user->name}` from `{$userLocation->location->name}`."
                        . "<br/>Reason: " . $reason,
                ]
            ])
                ->setFrom(\Yii::$app->params['notificationEmail'])
                ->setTo(\Yii::$app->params['adminEmail'])
                ->setSubject(\Yii::$app->params['project-name'] . ' - ' . \Yii::t("app", 'User removal request'))
                ->send();
        }
        return ['success' => true];
    }

    public function actionManageEquipments()
    {
        $this->isPost();
        $location_id = Yii::$app->request->post("location_id");
        $units = Yii::$app->request->post("units");

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['user_id' => $user->id],
                //['role' => UserLocation::ROLE_DECISION_MAKER],
            ])
            ->one();

        if (!empty($userLocation)) {
            $removed_units = [];
            foreach ($units as $unit_id => $status) {
                if (!(bool)$status) {
                    $removed_units[] = $unit_id;
                }
            }
            $userLocation->removed_units = implode(",", $removed_units);
            $userLocation->save();
        }

        return $units;
    }
}
