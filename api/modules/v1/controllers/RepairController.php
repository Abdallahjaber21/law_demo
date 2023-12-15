<?php


namespace api\modules\v1\controllers;


use common\components\exceptions\FailedToLoadDataException;
use common\components\extensions\api\ApiController;
use common\components\notification\Notification;
use common\models\RepairRequest;
use common\models\RepairRequestRating;
use common\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class RepairController extends ApiController
{

    public function actionRequest()
    {
        $this->isPost();

        $repairRequest = new RepairRequest();
        $repairRequest->user_id = \Yii::$app->getUser()->getId();
        $repairRequest->type = RepairRequest::TYPE_REQUEST;
        $repairRequest->status = RepairRequest::STATUS_DRAFT;
        $repairRequest->requested_at = gmdate("Y-m-d H:i:s"); //new Expression("now()");
        if ($repairRequest->load(\Yii::$app->getRequest()->post(), "")) {
            if ($repairRequest->problem_id == -1) {
                $repairRequest->problem_id = null;
                if (empty($repairRequest->problem_input)) {
                    $repairRequest->problem_id = -1;
                }
            }
            if ($repairRequest->schedule == RepairRequest::SCHEDULE_RIGHT_NOW) {
                $repairRequest->scheduled_at = gmdate('Y-m-d H:i:s');
                if (!empty($repairRequest->equipment)) {
                    if (!$repairRequest->equipment->isSameDayService()) {
                        $repairRequest->extra_cost = 100; //TODO What about extra cost???
                    }
                }
            }
            if ($repairRequest->schedule == RepairRequest::SCHEDULE_NEXT_BUSINESS_DAY) {
                $repairRequest->scheduled_at = gmdate('Y-m-d 08:00:00', strtotime('+1 Weekday'));
            }
            if ($repairRequest->schedule == RepairRequest::SCHEDULE_SCHEDULED) {
                //Set to customer selected date time
                //type and scheduled_at date are sent from mobile app as selected
                if (!empty($repairRequest->equipment)) {
                    $date = gmdate("Y-m-d H:i:s", strtotime($repairRequest->scheduled_at));
                    \Yii::error($date);
                    if ($repairRequest->equipment->checkIfExtraCostApplies($date)) {
                        $repairRequest->extra_cost = 100;
                    }
                }
            }
            if ($repairRequest->save()) {
                $repairRequest->refresh();
                Notification::notifyAdmins(
                    "You received a new request #{$repairRequest->id}",
                    "New request received",
                    [],
                    ['/repair-request/view', 'id' => $repairRequest->id],
                    [],
                    Notification::TYPE_NOTIFICATION,
                    $repairRequest->id
                );

                Notification::notifyEquipmentUsers(
                    $repairRequest->equipment,
                    "You requested a new repair #{$repairRequest->id}",
                    "New request sent",
                    [],
                    ['/site/index'],
                    ['action' => 'view-service', 'id' => $repairRequest->id],
                    null,
                    Notification::TYPE_NOTIFICATION,
                    $repairRequest->id
                );
                return $repairRequest;
            } else {
                $firstErrors = $repairRequest->getFirstErrors();
                throw new BadRequestHttpException(array_values($firstErrors)[0]);
            }
        } else {
            throw new FailedToLoadDataException();
        }
    }

    public function actionHistory($from, $to)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests2()
            ->select(["id", "scheduled_at"])
            ->where([
                'AND',
                ['>=', 'scheduled_at', $from],
                ['<=', 'scheduled_at', $to],
            ])
            ->asArray()
            ->all();
    }

    public function actionOndate($date)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests2()
            ->where([
                'AND',
                ['>=', 'scheduled_at', "{$date} 00:00:00"],
                ['<=', 'scheduled_at', "{$date} 23:59:59"],
            ])
            ->all();
    }

    public function actionActiveRequests()
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests2()
            ->where([
                //                'OR',
                //                [
                'AND',
                ['type' => RepairRequest::TYPE_REQUEST],
                ['!=', 'status', RepairRequest::STATUS_DRAFT],
                [
                    'OR',
                    ['is', 'departed_at', null],
                    ['>=', 'departed_at', gmdate("Y-m-d H:i:s", strtotime("-1 week"))],
                ]

                //                ],
                //                [
                //                    'AND',
                //                    ['type' => RepairRequest::TYPE_MAINTENANCE],
                //                    [
                //                        'status' => [
                //                            RepairRequest::STATUS_CHECKED_IN,
                //                            RepairRequest::STATUS_COMPLETED,
                //                            
                //                            RepairRequest::STATUS_INFORMED
                //                        ]
                //                    ]
                //                ]
            ])
            ->indexBy("id")
            ->all();
    }

    public function actionRequestDetails($id)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $request = $user->getRepairRequests2()
            ->where(['id' => $id])
            ->one();
        if (!empty($request)) {
            return $request;
        }
        throw new ServerErrorHttpException("Requested repair no longer exists");
    }

    public function actionMyRating($id)
    {
        $this->isGet();
        $user_id = \Yii::$app->getUser()->getId();

        return RepairRequestRating::find()
            ->where([
                'AND',
                ['user_id' => $user_id],
                ['repair_request_id' => $id],
            ])->one();
    }

    public function actionRate($id)
    {
        $this->isPost();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests2()
            ->where(['id' => $id])
            ->one();
        $rating = \Yii::$app->getRequest()->post("rating");
        if (!empty($request)) {
            $count = RepairRequestRating::find()
                ->where([
                    'AND',
                    ['repair_request_id' => $request->id],
                    ['user_id' => $user->id]
                ])
                ->count();
            if ($count == 0) {
                $repairRequestRating = new RepairRequestRating();
                $repairRequestRating->repair_request_id = $request->id;
                $repairRequestRating->user_id = $user->id;
                $repairRequestRating->rating = $rating;
                if ($repairRequestRating->save()) {
                    Notification::deleteAll([
                        'AND',
                        ['relation_key' => $id],
                        ['account_id' => $user->account_id]
                    ]);
                    $request->log("Rated the service {$rating} stars");
                    Notification::notifyTechnician(
                        $request->technician_id,
                        "User rated your repair service #{$id}",
                        "User rated your service",
                        [],
                        ['/site/index'],
                        ['action' => 'view-service', 'id' => $id],
                        null,
                        Notification::TYPE_NOTIFICATION,
                        $id
                    );
                }
            } else {
                throw new ServerErrorHttpException("You already rated this service");
            }
            $request->rating = RepairRequestRating::find()
                ->where(['repair_request_id' => $id])
                ->average('rating');
            $request->save(false);
        }
        return ['success' => true];
    }
}
