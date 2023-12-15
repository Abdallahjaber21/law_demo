<?php


namespace technician\modules\v1\controllers;


use common\components\exceptions\FailedToLoadDataException;
use common\components\extensions\api\ApiController;
use common\components\helpers\Distance;
use common\components\helpers\ImageUploadHelper;
use common\components\notification\Notification;
use common\components\settings\Setting;
use common\models\Account;
use common\models\AdminNotifications;
use common\models\Assignee;
use common\models\CompletedMaintenanceTask;
use common\models\CoordinatesIssue;
use common\models\Division;
use common\models\EngineOilTypes;
use common\models\Equipment;
use common\models\EquipmentCaValue;
use common\models\EquipmentMaintenanceBarcode;
use common\models\EquipmentType;
use common\models\Gallery;
use common\models\Image;
use common\models\LineItem;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Log;
use common\models\Maintenance;
use common\models\MaintenanceTaskGroup;
use common\models\MaintenanceVisit;
use common\models\MallPpmTasksHistory;
use common\models\OilChangeDue;
use common\models\PlantPpmTasks;
use common\models\PlantPpmTasksHistory;
use common\models\PpmAdditionalTasksValues;
use common\models\Problem;
use common\models\RepairRequest;
use common\models\RepairRequestChats;
use common\models\RepairRequestMaintenanceTask;
use common\models\Technician;
use common\models\TechnicianLocation;
use common\models\User;
use common\models\VehicleOilChangeHistory;
use common\models\VillaPpmTasksHistory;
use common\models\Worker;
use common\models\WorkerSector;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class RepairController extends ApiController
{
    public function actionCanCheckin($id)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where(["id" => $id])
            ->one();
        //type of request i want to check in to
        $type = $request->type;
        //
        $activeRepair = $user->getRepairRequests()
            ->where([
                'AND',
                [
                    'status' => [
                        RepairRequest::STATUS_CHECKED_IN,

                    ]
                ],
                ['type' => RepairRequest::TYPE_REQUEST],
                ['!=', 'id', $id]
            ])->one();
        //->count();
        $activeRequestCount = !empty($activeRepair) ? 1 : 0;

        $activeWork = $user->getRepairRequests()
            ->where([
                'AND',
                [
                    'status' => [
                        RepairRequest::STATUS_CHECKED_IN,

                    ]
                ],
                ['type' => RepairRequest::TYPE_SCHEDULED],
                ['!=', 'id', $id]
            ])->one();
        $activeWorksCount = !empty($activeWork) ? 1 : 0;

        $canEnRoute = true;
        $canCheckin = true;
        $canSubmitReport = true;
        $reason = '';
        if ($type == RepairRequest::TYPE_REQUEST) { //can i go to a repair?
            if ($activeRequestCount > 0) {
                $canEnRoute = false;
                $canCheckin = false;
                $reason = "Repair ID#{$activeRepair->id}"; //'active Request 1';
            }
        }

        if ($type == RepairRequest::TYPE_SCHEDULED) { //can i go to works?
            if ($activeRequestCount > 0 || $activeWorksCount > 0) {
                $canEnRoute = false;
                $canCheckin = false;
            }

            if ($activeRequestCount > 0) {
                $canSubmitReport = false;
                $reason = "Repair ID#{$activeRepair->id}";
            }
            if ($activeWorksCount > 0) {
                $reason = "Work ID#{$activeWork->id}";
            }
        }

        ///check if active maintenance
        $activeMaintenance = MaintenanceVisit::find()
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['status' => [MaintenanceVisit::STATUS_ENABLED]]
            ])->one();
        if (!empty($activeMaintenance)) {
            $canCheckin = false;
            $canEnRoute = false;
            //$reason = "has Active Maintenance";
            $reason = "Maintenance {$activeMaintenance->location->code}";
        }

        return [
            'can_checkin' => true,
            //(int)$canCheckin,
            'can_en_route' => true,
            //(int)$canEnRoute,
            'can_submit' => true,
            //(int)$canSubmitReport,
            'reason' => $reason
        ];
    }

    public function actionMaintenanceSummary()
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();


        $query = Maintenance::find()
            ->select(['count(*) as count'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.year' => (int) gmdate("Y")],
                [Maintenance::tableName() . '.month' => (int) gmdate("m")],
            ]);
        //        $query->andFilterWhere([Maintenance::tableName() . '.status' => $code]);
        $query->andFilterWhere([Maintenance::tableName() . '.technician_id' => $user->id]);
        $total = $query->scalar();

        $query = Maintenance::find()
            ->select(['count(*) as count'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.year' => (int) gmdate("Y")],
                [Maintenance::tableName() . '.month' => (int) gmdate("m")],
            ]);
        $query->andFilterWhere([Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE]);
        $query->andFilterWhere([Maintenance::tableName() . '.technician_id' => $user->id]);
        $completed = $query->scalar();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        $query = Maintenance::find()
            ->select(['count(*) as count'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.year' => (int) gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                [Maintenance::tableName() . '.month' => (int) gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
            ]);
        //        $query->andFilterWhere([Maintenance::tableName() . '.status' => $code]);
        $query->andFilterWhere([Maintenance::tableName() . '.technician_id' => $user->id]);
        $prevtotal = $query->scalar();

        $query = Maintenance::find()
            ->select(['count(*) as count'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.year' => (int) gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                [Maintenance::tableName() . '.month' => (int) gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
            ]);
        $query->andFilterWhere([Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE]);
        $query->andFilterWhere([Maintenance::tableName() . '.technician_id' => $user->id]);
        $prevcompleted = $query->scalar();

        return [
            'total' => (int) ($total),
            'completed' => (int) ($completed),
            'pending' => (int) ($total - $completed),
            'month' => gmdate("M Y"),
            'prev_total' => (int) ($prevtotal),
            'prev_completed' => (int) ($prevcompleted),
            'prev_pending' => (int) ($prevtotal - $prevcompleted),
            'prev_month' => gmdate("M Y", strtotime(gmdate("Y-m-1") . ' -2 day')),
        ];
    }

    public function actionActiveServices()
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests()
            ->where([
                'AND',
                [
                    'status' => [
                        RepairRequest::STATUS_CHECKED_IN,

                    ]
                ]
            ])
            ->orderBy(['type' => SORT_ASC])
            ->indexBy("id")
            ->all();
    }

    public function actionActiveServicesSplit()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairOrders();
    }

    public function actionScheduledServices()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getPpmRepairOrders();
    }
    public function actionCompletedServices()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getCompletedRepairOrders();
    }

    public function actionTodayServices()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getTodayRepairOrders();
    }

    // public function actionPendingServices()
    // {
    //     $this->isGet();

    //     /* @var $user Technician */
    //     $user = \Yii::$app->getUser()->getIdentity();

    //     return $user->getRepairRequests()
    //         ->where([
    //             'AND',
    //             //                ['>=', 'scheduled_at', date("Y-m-d 00:00:00")],
    //             //                ['<=', 'scheduled_at', date("Y-m-d 23:59:59")],
    //             ['status' => RepairRequest::STATUS_CREATED],
    //             ['type' => RepairRequest::TYPE_REQUEST]
    //         ])
    //         ->indexBy("id")
    //         ->all();
    // }

    public function actionUpcomingServices()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getUpcomingRepairOrders();
    }

    public function actionPendingWorks()
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests()
            ->where([
                'AND',
                //                ['>=', 'scheduled_at', date("Y-m-d 00:00:00")],
                //                ['<=', 'scheduled_at', date("Y-m-d 23:59:59")],
                ['status' => RepairRequest::STATUS_CREATED],
                ['type' => RepairRequest::TYPE_SCHEDULED]
            ])
            ->indexBy("id")
            ->all();
    }

    public function actionClearNotifications($id)
    {
        Notification::updateAll(['seen' => true, 'status' => Notification::STATUS_DISABLED], ['account_id' => $id]);

        return true;
    }

    public function actionChangeTeam($id)
    {
        $this->isPost();

        $repairRequest = RepairRequest::findOne($id);

        $datetime = Yii::$app->request->post('datetime');
        $technicians = Yii::$app->request->post('technicians');
        $technician_from_another_division = Yii::$app->request->post('technician_from_another_division');

        if (!empty($technicians)) {
            // Assignee::deleteAll(['repair_request_id' => $id]);

            // foreach ($technicians as $technician) {
            //     $assignee_model = new Assignee();
            //     $assignee_model->repair_request_id = $id;
            //     $assignee_model->user_id = $technician['id'];
            //     $assignee_model->datetime = $datetime;

            //     if (isset($technician['status']) && !empty($technician['status'])) {
            //         $assignee_model->status = $technician['status'];
            //     }

            //     if (!$assignee_model->save()) {
            //         return $assignee_model->errors;
            //     }
            // }

            $assignees = $repairRequest->checkMissingAssignees($technicians, true);

            // if (!empty($assignees)) {
            //     foreach ($assignees as $assignee) {

            //         if (isset($technician['status']) && !empty($technician['status'])) {
            //             $assignee->status = $assignee['status'];
            //         }

            //         if (!$assignee->save()) {
            //             return $assignee->errors;
            //         }
            //     }
            // }

            if (isset($technician_from_another_division) && !empty($technician_from_another_division)) {
                if ($technician_from_another_division === "true") {
                    if ($repairRequest->technician_from_another_division != 1) {
                        $repairRequest->technician_from_another_division = 1;
                        $repairRequest->save();
                        Log::AddLog(null, $repairRequest->id, Log::TYPE_REPAIR_REQUEST, "Work Order Updates", $repairRequest->getAttributeLabel("technician_from_another_division") . " was changed to: true", $repairRequest->status);
                    }
                } else {
                    if ($repairRequest->technician_from_another_division != 0) {
                        $repairRequest->technician_from_another_division = 0;
                        $repairRequest->save();
                        Log::AddLog(null, $repairRequest->id, Log::TYPE_REPAIR_REQUEST, "Work Order Updates", $repairRequest->getAttributeLabel("technician_from_another_division") . " was changed to: false", $repairRequest->status);
                    }

                }

            }
        }

        return $repairRequest;
    }

    public function actionChangeTeamLeader($id)
    {
        $this->isPost();

        $repairRequest = RepairRequest::findOne($id);

        $team_leader_id = Yii::$app->request->post('team_leader');

        if (!empty($team_leader_id)) {
            $repairRequest->team_leader_id = $team_leader_id;
            if ($repairRequest->save()) {
                Log::AddLog($team_leader_id, $repairRequest->id, Log::TYPE_TECHNICIAN, "Team Leader Modification", "Team Leader Set To: " . $repairRequest->teamLeader->name, Assignee::STATUS_FREE);
            }
            $repairRequest->refresh();
        }

        return $repairRequest;
    }

    public function actionCompleteService($id)
    {
        $repairRequest = RepairRequest::findOne($id);
        $status = Yii::$app->request->post('status');
        $data = Yii::$app->request->post('data');

        $user = \Yii::$app->getUser()->getIdentity();

        if (!empty($status)) {
            if ($status == RepairRequest::STATUS_COMPLETED) {

                $repairRequest->departed_at = gmdate("Y-m-d H:i:s");
                $repairRequest->status = RepairRequest::STATUS_COMPLETED;
                $repairRequest->completed_at = gmdate("Y-m-d H:i:s");
                $repairRequest->completed_by = $user->id;

                // Assignees
                $assignees = $repairRequest->assignees;
                foreach ($assignees as $ass) {
                    $ass->status = Assignee::STATUS_FREE;
                    $ass->save();
                }
            } else if ($status == RepairRequest::STATUS_REQUEST_COMPLETION) {

                if ($repairRequest->division_id == Division::DIVISION_PLANT) {
                    if (Account::getTechnicianAccountTypeLabel($user->id) == 'coordinator') {
                        $repairRequest->status = RepairRequest::STATUS_REQUEST_COMPLETION;
                    } else { // technician
                        $assignees = Assignee::find()->where(['repair_request_id' => $id])->all();

                        if (!empty($assignees)) {
                            foreach ($assignees as $tech) {
                                if (Account::getTechnicianAccountTypeLabel($tech->user_id) == 'coordinator') {
                                    $repairRequest->checkNotificationEmergency($repairRequest->urgent_status, $tech->user_id, "Technician Request Completion", "Technician {$user->name} Requested Completing Service #{$repairRequest->id}");
                                }
                            }
                        }
                    }
                } else {
                    $repairRequest->status = RepairRequest::STATUS_REQUEST_COMPLETION;
                    $repairRequest->checkNotificationEmergency($repairRequest->urgent_status, $repairRequest->owner_id, "Technician Request Completion", "Technician {$user->name} Requested Completing Service #{$repairRequest->id}");
                }

                $repairRequest->departed_at = gmdate("Y-m-d H:i:s");
            } else if ($status == RepairRequest::STATUS_NOT_COMPLETED) {
                $repairRequest->status = RepairRequest::STATUS_NOT_COMPLETED;

                $assignees = $repairRequest->assignees;

                foreach ($assignees as $assignee) {
                    if ($assignee->id != $user->id)
                        $repairRequest->checkNotificationEmergency($repairRequest->urgent_status, $assignee->id, "Work Not Completed", "Supervisor {$repairRequest->owner->name} Requests Redoing the job #{$repairRequest->id}");
                }
            }

            if (!empty($data)) {

                $repairRequest->note = ($data['note'] == "false") ? null : $data['note'];
                $repairRequest->supervisor_note = ($data['supervisor_note'] == "false") ? null : $data['supervisor_note'];

                $images = @$data['images'];

                $gallery = $repairRequest->gallery;

                if (!empty($gallery->images)) {
                    Image::deleteAll(['gallery_id' => @$repairRequest->gallery_id]);
                }

                if (!empty($images)) {
                    if (empty($gallery)) {
                        $gallery = new Gallery();
                        if ($gallery->save()) {
                            $repairRequest->gallery_id = $gallery->id;
                            $repairRequest->save(false);
                        }
                    }

                    foreach ($images as $index => $image) {
                        $imageModel = new Image();
                        $imageModel->gallery_id = $gallery->id;
                        if (is_array($image)) {
                            $imageModel->note = $image['note'];
                        }
                        $imageModel->save();
                        if (is_array($image)) {
                            ImageUploadHelper::uploadBase64Image($image['image'], $imageModel);
                        } else {
                            ImageUploadHelper::uploadBase64Image($image, $imageModel);
                        }
                    }
                }


                if (empty($repairRequest->customer_signature)) {
                    $signature = @$data['customer_signature'];
                    if (!empty($signature)) {
                        ImageUploadHelper::uploadBase64Image($signature, $repairRequest, "customer_signature");
                    }
                }

                if (empty($repairRequest->technician_signature)) {
                    $signature2 = @$data['technician_signature'];
                    if (!empty($signature2)) {
                        ImageUploadHelper::uploadBase64Image($signature2, $repairRequest, "technician_signature");
                    }
                }

                if (empty($repairRequest->supervisor_signature)) {
                    $signature3 = @$data['supervisor_signature'];
                    if (!empty($signature3)) {
                        ImageUploadHelper::uploadBase64Image($signature3, $repairRequest, "supervisor_signature");
                    }
                }

                if (empty($repairRequest->coordinator_signature)) {
                    $signature4 = @$data['coordinator_signature'];
                    if (!empty($signature4)) {
                        ImageUploadHelper::uploadBase64Image($signature4, $repairRequest, "coordinator_signature");
                    }
                }
            }

            if ($repairRequest->save()) {

                $admin_notification = new AdminNotifications();
                $admin_notification->request_id = $repairRequest->id;
                $admin_notification->technician_id = Yii::$app->user->id;
                $admin_notification->type = AdminNotifications::TYPE_STATUS;
                $admin_notification->seen = false;
                $admin_notification->status = $status;
                $admin_notification->save(false);

                Log::AddLog(null, $repairRequest->id, Log::TYPE_REPAIR_REQUEST, "Work Order Completion", "Work Order #{$repairRequest->id} Status Changed To: " . (new RepairRequest())->status_list[$repairRequest->status], $repairRequest->status);
                return $repairRequest;
            } else {
                print_r($repairRequest->errors);
                exit;
            }
        }

        return null;
    }

    public function actionApprove($id)
    {
        $this->isPost();

        $order_request = RepairRequest::findOne($id);

        $order_request->status = RepairRequest::STATUS_CREATED;

        $order_request->save();
        $order_request->refresh();
        Log::AddLog(null, $order_request->id, Log::TYPE_REPAIR_REQUEST, "Work Order Approval", "Work Order Was Approved By: " . Yii::$app->user->identity->name, $order_request->status);

        $assignees = $order_request->assignees;

        if (!empty($assignees)) {
            foreach ($assignees as $assignee) {
                $assignee->status = Assignee::STATUS_ASSIGNED;
                if ($assignee->save()) {
                    Log::AddLog($assignee->user_id, $order_request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $assignee->user->name . " Was Assigned", Assignee::STATUS_ASSIGNED);
                }
            }
        }


        return $order_request;
    }

    public function actionReject($id)
    {
        $this->isPost();

        $reason = \Yii::$app->getRequest()->post("reason");

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = RepairRequest::findOne($id);
        $assignees = ArrayHelper::getColumn($request->assignees, 'user_id');

        if (!empty($request)) {
            $request->note = $reason;
            $request->status = RepairRequest::STATUS_CANCELLED;
            $request->save();
            $request->refresh();

            Log::AddLog(null, $request->id, Log::TYPE_REPAIR_REQUEST, "Work Order Rejection", "Work Order Was Declined By: " . Yii::$app->user->identity->name, $request->status);

            if (!empty($assignees)) {
                $request->checkMissingAssigneesWithoutStatus($assignees, true);
            }

            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionRejectMember($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = Assignee::find()->where(['repair_request_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        /* @var $request RepairRequest */
        $request = RepairRequest::findOne($id);

        if (!empty($request)) {
            Log::AddLog($user->user_id, $request->id, Log::TYPE_TECHNICIAN, "Technician Removal", "Technician: " . $user->user->name . " Was Removed", Assignee::STATUS_FREE);
            $user->delete();

            $request->refresh();
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionChangeStatus($id)
    {
        $this->isPost();

        $new_status = \Yii::$app->getRequest()->post("status");

        /* @var $request RepairRequest */
        $request = RepairRequest::findOne($id);
        $assignees = $request->assignees;
        $assignee_status = null;

        if (!empty($request)) {

            $statuses = ArrayHelper::merge((new Assignee())->status_list, (new Assignee())->acceptance_status_list);

            if (!empty($new_status)) {
                if ($new_status == RepairRequest::STATUS_CHECKED_IN) {

                    if (!empty($request->location_id)) {
                        $location_model = $request->location;
                        if ((empty($location_model->longitude) || $location_model->longitude == '') && (empty($location_model->latitude) || $location_model->latitude == '')) {

                            $user_location = Yii::$app->request->post('user_location');

                            if (!empty($user_location)) {
                                $coordinatesIssue = new CoordinatesIssue();
                                $coordinatesIssue->location_id = $location_model->id;
                                $coordinatesIssue->reported_by = Yii::$app->user->id;
                                $coordinatesIssue->old_latitude = "0";
                                $coordinatesIssue->old_longitude = "0";
                                $coordinatesIssue->new_latitude = $user_location['latitude'];
                                $coordinatesIssue->new_longitude = $user_location['longitude'];
                                $coordinatesIssue->status = CoordinatesIssue::STATUS_PENDING;

                                // $location_model->latitude = $user_location['latitude'];
                                // $location_model->longitude = $user_location['longitude'];
                                if (!$coordinatesIssue->save()) {
                                    print_r($coordinatesIssue->errors);
                                    exit;
                                }
                                Log::AddLog(null, $request->id, Log::TYPE_REPAIR_REQUEST, "Coordinates Changement", "Work Order Coordinates Has Been Changed To: {$user_location['latitude']} - {$user_location['longitude']}", $new_status);
                            }
                            // Get current user coordinates and update the location by it + save()
                        }
                    }

                    $request->arrived_at = gmdate("Y-m-d H:i:s");
                    $assignee_status = Assignee::STATUS_BUSY;
                } else if ($new_status == RepairRequest::STATUS_ON_HOLD || $new_status == RepairRequest::STATUS_NOT_COMPLETED || $new_status == RepairRequest::STATUS_REQUEST_COMPLETION) {
                    $assignee_status = Assignee::STATUS_CHECKED_OUT;
                } else if ($new_status == RepairRequest::STATUS_COMPLETED || $new_status == RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN) {
                    $assignee_status = Assignee::STATUS_FREE;
                }

                if (!empty($assignees) && !empty($assignee_status)) {
                    foreach ($assignees as $assignee) {
                        $assignee->status = $assignee_status;
                        if ($assignee->save()) {
                            Log::AddLog($assignee->user_id, $request->id, Log::TYPE_TECHNICIAN, "Technician Status", "Technician: " . $assignee->user->name . " Status Changed to: " . $statuses[$assignee_status], $assignee_status);
                        }
                    }
                }
            }
            $request->status = $new_status;

            if ($request->save()) {

                $admin_notification = new AdminNotifications();
                $admin_notification->request_id = $request->id;
                $admin_notification->technician_id = Yii::$app->user->id;
                $admin_notification->type = AdminNotifications::TYPE_STATUS;
                $admin_notification->seen = false;
                $admin_notification->status = $new_status;
                $admin_notification->save(false);

                Log::AddLog(null, $request->id, Log::TYPE_REPAIR_REQUEST, "Work Order Status", "Work Status Changed To: " . (new RepairRequest())->status_list[$new_status], $new_status);
            }
            $request->refresh();
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionCompletePpmTasks($id)
    {
        $this->isPost();

        $request = RepairRequest::findOne($id);
        $asset = LocationEquipments::findOne($request->equipment_id);

        if (!empty($request)) {

            // if ($request->service_type == RepairRequest::TYPE_PPM) {

            if ($request->division_id == Division::DIVISION_MALL) {
                $tasks = \Yii::$app->getRequest()->post("tasks");
                $additional = \Yii::$app->getRequest()->post("additional_tasks");

                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        $task_value_model = MallPpmTasksHistory::find()->where(['id' => $task['name']])->one();
                        $task_value_model->status = $task['value'];
                        $task_value_model->save();
                    }
                }

                if (!empty($additional)) {
                    foreach ($additional as $task) {
                        $task_value_model = PpmAdditionalTasksValues::find()->where(['ppm_service_id' => $request->id, 'asset_id' => $request->equipment_id, 'additional_task_id' => $task['name']])->one();

                        if (empty($task_value_model)) {
                            $task_value_model = new PpmAdditionalTasksValues();
                            $task_value_model->ppm_service_id = $request->id;
                            $task_value_model->additional_task_id = $task['name'];
                            $task_value_model->asset_id = $request->equipment_id;
                            $task_value_model->status = PpmAdditionalTasksValues::STATUS_ENABLED;
                        }

                        $task_value_model->value = $task['value'];
                        $task_value_model->save();
                    }
                }
            } else if ($request->division_id == Division::DIVISION_PLANT) {
                $tasks = \Yii::$app->getRequest()->post("tasks");
                $additional = \Yii::$app->getRequest()->post("additional_tasks");

                $meter_value = \Yii::$app->getRequest()->post("meter_value");
                $oil_change_type = \Yii::$app->getRequest()->post("oil_change_type");

                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        $task_value_model = PlantPpmTasksHistory::find()->where(['id' => $task['name']])->one();
                        $task_value_model->status = $task['value'];

                        if (isset($task['remark']))
                            $task_value_model->remarks = $task['remark'];
                        $task_value_model->save();
                    }
                }

                if (!empty($additional)) {
                    foreach ($additional as $task) {
                        $task_value_model = PlantPpmTasksHistory::find()->where(['id' => $task['name']])->one();
                        $task_value_model->status = $task['value'];

                        if (isset($task['remark']))
                            $task_value_model->remarks = $task['remark'];
                        $task_value_model->save();
                    }
                }

                if (!empty($meter_value)) {
                    $asset->meter_value = $meter_value;
                    $asset->save();
                }

                if (!empty($oil_change_type)) {

                    $existing_model = EngineOilTypes::findOne($oil_change_type);

                    $due_model = new VehicleOilChangeHistory();
                    $due_model->repair_request_id = $request->id;
                    $due_model->asset_id = $request->equipment_id;
                    $due_model->oil_id = $oil_change_type;
                    $due_model->meter_value = $meter_value;
                    $due_model->next_oil_change = @$meter_value + ($existing_model->oil_durability);
                    $due_model->save();
                }

            } else if ($request->division_id == Division::DIVISION_VILLA) {
                $tasks = \Yii::$app->getRequest()->post("tasks");

                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        $task_value_model = VillaPpmTasksHistory::find()->where(['id' => $task['name']])->one();
                        $task_value_model->status = $task['value'];

                        if (isset($task['remark']))
                            $task_value_model->remarks = $task['remark'];
                        $task_value_model->save();
                    }
                }
            }
            // }



            return $request;
        }

        throw new NotFoundHttpException("Request not found");
    }


    public function actionGetMessages($id)
    {
        $request = RepairRequest::findOne($id);

        $datetime = Yii::$app->request->get('datetime');

        if (empty($datetime)) {
            return $request->repairRequestChats;
        }

        return RepairRequestChats::find()->where(['>', 'created_at', $datetime])->all();
    }
    public function actionSaveMessages($id)
    {
        $request = RepairRequest::findOne($id);

        $user_id = Yii::$app->user->id;
        $message = Yii::$app->request->post('message');
        $image = Yii::$app->request->post('image');
        $audio = Yii::$app->request->post('audio');

        if (!empty($message)) {
            $model = new RepairRequestChats();
            $model->request_id = $id;
            $model->assignee_id = $user_id;
            $model->message = $message;
            if ($model->save()) {
                $request->refresh();

                if (!empty($image)) {
                    $gallery = $model->gallery;
                    if (empty($gallery)) {
                        $gallery = new Gallery();
                        if ($gallery->save()) {
                            $model->gallery_id = $gallery->id;
                            $model->save(false);
                        }
                    }

                    $imageModel = new Image();
                    $imageModel->gallery_id = $gallery->id;
                    $imageModel->save();
                    ImageUploadHelper::uploadBase64Image($image, $imageModel);
                }

                if (!empty($audio)) {
                    list(, $audio) = explode(';', $audio);
                    list(, $audio) = explode(',', $audio);
                    $audioData = base64_decode($audio);

                    // Generate a unique filename
                    $filename = uniqid('audio_') . '.mp3'; // Change '.mp3' based on your audio file format

                    // Save the file to the uploads directory
                    $Directory = Yii::getAlias('@static/upload/audio/');

                    if (!file_exists($Directory)) {
                        mkdir($Directory, 0777, true);
                    }

                    file_put_contents($Directory . $filename, $audioData);

                    $audioData = base64_decode($audio);

                    // Update the model with the filename
                    $model->audio = $filename;
                    if (!$model->save()) {
                        print_r($model->errors);
                        exit;
                    }
                }
            }
        }

        return $request;
    }



    // public function actionApprove($id)
    // {
    //     $this->isPost();

    //     /* @var $user Technician */
    //     $user = \Yii::$app->getUser()->getIdentity();

    //     /* @var $request RepairRequest */
    //     $request = $user->getRepairRequests()
    //         ->where([
    //             'AND',
    //             ["id" => $id],
    //             //["status" => RepairRequest::STATUS_CREATED],
    //         ])
    //         ->one();

    //     TechnicianLocation::deleteAll(['technician_id' => $user->id]);

    //     $lat = \Yii::$app->getRequest()->post("latitude");
    //     $lng = \Yii::$app->getRequest()->post("longitude");
    //     $delay = \Yii::$app->getRequest()->post("delay");


    //     (new TechnicianLocation([
    //         'latitude'      => $lat,
    //         'longitude'     => $lng,
    //         'technician_id' => $user->id
    //     ]))->save();
    //     $user->refresh();

    //     if (!empty($request)) {
    //         //            $routeETA = ceil($request->getRouteETA());
    //         //            if (!empty($delay)) {
    //         //                $routeETA += $delay * 60;
    //         //            }
    //         $request->status = RepairRequest::STATUS_INFORMED;
    //         //            $request->eta = new Expression("(now() + INTERVAL {$routeETA} SECOND)");
    //         $request->informed_at = new Expression("now()");
    //         $request->save();
    //         $request->refresh();
    //         Notification::deleteAll([
    //             'AND',
    //             ['relation_key' => $id],
    //             ['account_id' => $user->id]
    //         ]); //Delete all past notifications for this request

    //         $request->log("Approved the service");
    //         return $request;
    //     }
    //     throw new NotFoundHttpException("Request not found");
    // }

    // public function actionEnRoute($id)
    // {
    //     $this->isPost();

    //     /* @var $user Technician */
    //     $user = \Yii::$app->getUser()->getIdentity();

    //     /* @var $request RepairRequest */
    //     $request = $user->getRepairRequests()
    //         ->where([
    //             'AND',
    //             ["id" => $id],
    //             ["NOT IN", "status", [
    //                 RepairRequest::STATUS_CHECKED_IN,
    //                 RepairRequest::STATUS_COMPLETED,

    //                 RepairRequest::STATUS_COMPLETED,
    //             ]],
    //         ])
    //         ->one();
    //     if ($request->type == RepairRequest::TYPE_SCHEDULED) {
    //         if (gmdate("Y-m-d") < gmdate("Y-m-d", strtotime($request->scheduled_at))) {
    //             $request->scheduled_at = gmdate("Y-m-d H:i:s");
    //         }
    //     }

    //     TechnicianLocation::deleteAll(['technician_id' => $user->id]);

    //     $lat = \Yii::$app->getRequest()->post("latitude");
    //     $lng = \Yii::$app->getRequest()->post("longitude");
    //     $delay = \Yii::$app->getRequest()->post("delay");
    //     $method = \Yii::$app->getRequest()->post("method");
    //     $etaMethod = null;
    //     if (in_array($method, [
    //         'driving-car',
    //         'cycling-regular',
    //         'foot-walking',
    //     ])) {
    //         $etaMethod = $method;
    //     }


    //     (new TechnicianLocation([
    //         'latitude'      => $lat,
    //         'longitude'     => $lng,
    //         'technician_id' => $user->id
    //     ]))->save();
    //     $user->refresh();

    //     if (!empty($request)) {
    //         $request->status = RepairRequest::STATUS_EN_ROUTE;
    //         $request->save();
    //         $routeETA = 0;
    //         try {
    //             $routeETA = ceil($request->getRouteETA($etaMethod));
    //             if (!empty($delay)) {
    //                 $routeETA += $delay * 60;
    //             }
    //         } catch (BadRequestHttpException $ex) {
    //             $request->status = RepairRequest::STATUS_INFORMED;
    //             $request->save();
    //             throw $ex;
    //         }
    //         Yii::error($routeETA, "routeETA");
    //         $request->eta = new Expression("(now() + INTERVAL {$routeETA} SECOND)");
    //         $request->informed_at = new Expression("now()");
    //         $request->save();
    //         $request->refresh();
    //         $request->log("En-route");
    //         Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
    //         if ($request->type == RepairRequest::TYPE_REQUEST) {
    //             Notification::notifyEquipmentUsers(
    //                 $request->equipment,
    //                 "Technician en route",
    //                 "A technician is on his way",
    //                 [],
    //                 ['/site/index'],
    //                 ['action' => 'view-service', 'id' => $request->id],
    //                 null,
    //                 Notification::TYPE_NOTIFICATION,
    //                 $request->id
    //             );
    //         }
    //         return $request;
    //     }
    //     throw new NotFoundHttpException("Request not found");
    // }

    public function actionAddDelay($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                ["status" => RepairRequest::STATUS_EN_ROUTE],
            ])
            ->one();
        $delay = \Yii::$app->getRequest()->post("delay");

        if (!empty($request)) {
            $routeETA = $delay * 60;
            $request->eta = new Expression("(eta + INTERVAL {$routeETA} SECOND)");
            $request->save();
            $request->log("Added delay: {$delay} minutes [at " . date("H:i:s") . "]");
            $request->refresh();
            if ($request->type == RepairRequest::TYPE_REQUEST) {
                //Send a delay notificaiton
                Notification::notifyEquipmentUsers(
                    $request->equipment,
                    "Technician en route",
                    "We're sorry, Technician will arrive a bit late",
                    [],
                    ['/site/index'],
                    ['action' => 'view-service', 'id' => $request->id],
                    null,
                    Notification::TYPE_NOTIFICATION,
                    $request->id
                );
            }
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    // public function actionReject($id)
    // {
    //     $this->isPost();

    //     $reason = \Yii::$app->getRequest()->post("reason");
    //     $isReschedule = \Yii::$app->getRequest()->post("reschedule");

    //     /* @var $user Technician */
    //     $user = \Yii::$app->getUser()->getIdentity();

    //     /* @var $request RepairRequest */
    //     $request = $user->getRepairRequests()
    //         ->where([
    //             'AND',
    //             ["id" => $id],
    //             ["status" => [RepairRequest::STATUS_CHECKED_IN, RepairRequest::STATUS_CREATED,  RepairRequest::STATUS_EN_ROUTE]],
    //         ])
    //         ->one();
    //     if (!empty($request)) {
    //         $request->rejection_reason = $reason;
    //         $request->status = RepairRequest::STATUS_DRAFT;
    //         $request->technician_id = null;
    //         $request->save();
    //         $request->refresh();
    //         if (!empty($isReschedule)) {
    //             $request->log("Requested Reschedule ($reason)");
    //         } else {
    //             $request->log("Rejected service ($reason)");
    //         }
    //         Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
    //         return $request;
    //     }
    //     throw new NotFoundHttpException("Request not found");
    // }

    public function actionArrive($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_INFORMED],
                ["status" => RepairRequest::STATUS_EN_ROUTE],
            ])
            ->one();
        if (!empty($request)) {
            $request->status = RepairRequest::STATUS_CHECKED_IN;
            $request->arrived_at = new Expression("now()");
            //$request->confirmed_equipment = true;
            $request->save();
            $request->refresh();
            $request->log("Arrived");
            if ($request->type == RepairRequest::TYPE_REQUEST) {
                Notification::notifyEquipmentUsers(
                    $request->equipment,
                    "Service arrived",
                    "A technician arrived to your location",
                    [],
                    ['/site/index'],
                    ['action' => 'view-service', 'id' => $request->id],
                    null,
                    Notification::TYPE_NOTIFICATION,
                    $request->id
                );
            }
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionReport($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_CHECKED_IN],
            ])
            ->one();
        if (!empty($request)) {
            $request->status = RepairRequest::STATUS_COMPLETED;
            $request->departed_at = new Expression("now()");

            $customer_name = Yii::$app->getRequest()->post("customerName");
            $request->customer_name = $customer_name;

            if ($request->type == RepairRequest::TYPE_REQUEST) {
                $request->person_trapped = \Yii::$app->getRequest()->post("person_trapped");
                $request->system_operational = \Yii::$app->getRequest()->post("system_operational");
                $request->works_completed = null;
            } else {
                $request->person_trapped = false;
                $request->system_operational = true;
                $request->works_completed = \Yii::$app->getRequest()->post("works_completed");
            }
            $request->note = \Yii::$app->getRequest()->post("note");
            $request->note_client = \Yii::$app->getRequest()->post("note_client");
            //TODO mark request missing signature
            $request->missing_signature = \Yii::$app->getRequest()->post("withoutSignature");
            if ($request->save()) {
                $lineItems = \Yii::$app->getRequest()->post("lineitems");
                if (!empty($lineItems)) {
                    LineItem::deleteAll(['repair_request_id' => $request->id]);
                    foreach ($lineItems as $index => $lineItem) {
                        $lineItem1 = new LineItem();
                        $lineItem1->repair_request_id = $request->id;
                        $lineItem1->object_code_id = $lineItem['object'];
                        $lineItem1->damage_code_id = $lineItem['damage'];
                        $lineItem1->cause_code_id = $lineItem['cause'];
                        $lineItem1->manufacturer_id = $lineItem['manufacturer'];
                        $lineItem1->type = LineItem::TYPE_TECHNICIAN;
                        if ($lineItem1->save()) {
                        }
                    }
                }
                $maintenanceTasksReport = \Yii::$app->getRequest()->post("maintenanceTasksReport");
                if (!empty($maintenanceTasksReport)) {
                    foreach ($maintenanceTasksReport as $index => $maintenanceTask) {
                        //TODO Save report to repair service
                        $repairRequestMaintenanceTask = new RepairRequestMaintenanceTask();
                        $repairRequestMaintenanceTask->repair_request_id = $request->id;
                        $repairRequestMaintenanceTask->maintenance_task_group_id = $maintenanceTask;
                        $repairRequestMaintenanceTask->checked = true;
                        $repairRequestMaintenanceTask->save();
                    }
                }
                $images = \Yii::$app->getRequest()->post("images");
                if (!empty($images)) {
                    $gallery = $request->gallery;
                    if (empty($gallery)) {
                        $gallery = new Gallery();
                        if ($gallery->save()) {
                            $request->gallery_id = $gallery->id;
                            $request->save(false);
                        }
                    }
                    foreach ($images as $index => $image) {
                        $imageModel = new Image();
                        $imageModel->gallery_id = $gallery->id;
                        $imageModel->save();
                        ImageUploadHelper::uploadBase64Image($image, $imageModel);
                    }
                }
            }

            $signature = Yii::$app->getRequest()->post("signature");
            if (!empty($signature)) {
                ImageUploadHelper::uploadBase64Image($signature, $request, "customer_signature");
                //                $img = imagecreatefromjpeg($request->customer_signature_path);
                //                $white = imagecolorallocate($img, 255, 255, 255);
                //                imagecolortransparent($img, $white);
                //                imagepng($img, $request->customer_signature_path);
            }
            $signature2 = Yii::$app->getRequest()->post("signature2");
            if (!empty($signature2)) {
                ImageUploadHelper::uploadBase64Image($signature2, $request, "technician_signature");
                //                $img = imagecreatefromjpeg($request->customer_signature_path);
                //                $white = imagecolorallocate($img, 255, 255, 255);
                //                imagecolortransparent($img, $white);
                //                imagepng($img, $request->customer_signature_path);
            }
            $hard_copy_report = Yii::$app->getRequest()->post("hard_copy_report");
            if (!empty($hard_copy_report)) {
                ImageUploadHelper::uploadBase64Image($hard_copy_report, $request, "hard_copy_report");
            }

            $request->refresh();

            $request->generatePdfReport();

            $request->log("Submitted report");
            Notification::deleteAll(['relation_key' => $request->id]); //Delete all past notifications for this request
            if ($request->type == RepairRequest::TYPE_REQUEST) {
                if ($request->system_operational) {
                    Notification::notifyEquipmentUsers(
                        $request->equipment,
                        "Service complete.",
                        "Repair service is complete.",
                        [],
                        ['/site/index'],
                        ['action' => 'view-service', 'id' => $request->id],
                        null,
                        Notification::TYPE_NOTIFICATION,
                        $request->id
                    );
                } else {
                    Notification::notifyEquipmentUsers(
                        $request->equipment,
                        "Service not completed",
                        "Repair service is not complete.",
                        [],
                        ['/site/index'],
                        ['action' => 'view-service', 'id' => $request->id],
                        null,
                        Notification::TYPE_NOTIFICATION,
                        $request->id
                    );
                }
            }
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionReportPreview($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_CHECKED_IN],
            ])
            ->one();
        if (!empty($request)) {
            $request->departed_at = gmdate("Y-m-d H:i:s");
            if ($request->type == RepairRequest::TYPE_REQUEST) {
                $request->person_trapped = \Yii::$app->getRequest()->post("person_trapped");
                $request->system_operational = \Yii::$app->getRequest()->post("system_operational");
            } else {
                $request->person_trapped = false;
                $request->system_operational = true;
            }
            $request->note = \Yii::$app->getRequest()->post("note");
            $request->note_client = \Yii::$app->getRequest()->post("note_client");
            //if ($request->save()) {
            $lineItems = \Yii::$app->getRequest()->post("lineitems");
            $createdLineItems = [];
            if (!empty($lineItems)) {
                foreach ($lineItems as $index => $lineItem) {
                    $lineItem1 = new LineItem();
                    $lineItem1->repair_request_id = $request->id;
                    $lineItem1->object_code_id = $lineItem['object'];
                    $lineItem1->damage_code_id = $lineItem['damage'];
                    $lineItem1->cause_code_id = $lineItem['cause'];
                    $lineItem1->manufacturer_id = $lineItem['manufacturer'];
                    if (!$lineItem1->save()) {
                        //return $lineItem1->getErrors();
                    }
                    $createdLineItems[] = $lineItem1;
                }
            }
            //}

            //$request->refresh();
            $file = $request->generatePdfReport();
            foreach ($createdLineItems as $index => $createdLineItem) {
                LineItem::deleteAll(['repair_request_id' => $request->id]);
            }
            return [
                'file' => Yii::getAlias("@staticWeb/upload/reports/{$request->random_token}.pdf")
            ];
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionSignCustomer($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_CHECKED_IN],
            ])
            ->one();
        if (!empty($request)) {
            $customer_name = Yii::$app->getRequest()->post("customerName");
            $request->customer_name = $customer_name;
            $request->missing_signature = false;
            $signature = Yii::$app->getRequest()->post("signature");
            if (!empty($signature)) {
                ImageUploadHelper::uploadBase64Image($signature, $request, "customer_signature");
            }
            $request->refresh();
            $request->generatePdfReport(false, true);
            $request->log("Customer signed");

            Notification::deleteAll(['relation_key' => $request->id]); //Delete all past notifications for this request
            return $request;
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionReportView($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_CHECKED_IN],
            ])
            ->one();
        if (!empty($request)) {
            $path = Yii::getAlias("@static/upload/reports/client/{$request->random_token}.pdf");
            $url = Yii::getAlias("@staticWeb/upload/reports/client/{$request->random_token}.pdf");
            return ['file' => file_exists($path) ? $url : null];
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionReportFullView($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $request RepairRequest */
        $request = $user->getRepairRequests()
            ->where([
                'AND',
                ["id" => $id],
                //["status" => RepairRequest::STATUS_CHECKED_IN],
            ])
            ->one();
        if (!empty($request)) {
            $path = Yii::getAlias("@static/upload/reports/{$request->random_token}.pdf");
            $url = Yii::getAlias("@staticWeb/upload/reports/{$request->random_token}.pdf");
            return ['file' => file_exists($path) ? $url : null];
        }
        throw new NotFoundHttpException("Request not found");
    }

    public function actionHistory($from, $to)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        return $user->getRepairRequests()
            ->select(["id", "scheduled_at", 'status'])
            ->where([
                'OR',
                [
                    'AND',
                    ['>=', 'scheduled_at', "{$from} 00:00:00"],
                    ['<=', 'scheduled_at', "{$to} 23:59:59"],
                    [
                        'status' => [

                            RepairRequest::STATUS_CHECKED_IN,

                                //                            RepairRequest::STATUS_COMPLETED,
                            RepairRequest::STATUS_CREATED,
                            //                            RepairRequest::STATUS_COMPLETED,
                        ]
                    ]
                ],
                [
                    'AND',
                    ['>=', 'completed_at', "{$from} 00:00:00"],
                    ['<=', 'completed_at', "{$to} 23:59:59"],
                    [
                        'status' => [
                                //                            
                                //                            RepairRequest::STATUS_CHECKED_IN,
                                //                            
                            RepairRequest::STATUS_COMPLETED,
                                //                            RepairRequest::STATUS_CREATED,
                            RepairRequest::STATUS_COMPLETED,
                        ]
                    ]
                ],
            ])
            ->asArray()
            ->all();
    }

    public function actionReportHistory($query)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $repairs = RepairRequest::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',
                [RepairRequest::tableName() . '.technician_id' => $user->id],
                [RepairRequest::tableName() . '.type' => RepairRequest::TYPE_REQUEST],
                [RepairRequest::tableName() . '.status' => [RepairRequest::STATUS_COMPLETED, RepairRequest::STATUS_COMPLETED]],
                [
                    'OR',
                    ["like", RepairRequest::tableName() . '.id', $query],
                    ["like", Equipment::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.name', $query],
                ],
                ['>=', RepairRequest::tableName() . '.departed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            ->indexBy("id")
            //            ->createCommand()->rawSql;
            //->limit(20)
            ->orderBy(['departed_at' => SORT_DESC])
            ->all();

        $works = RepairRequest::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',
                [RepairRequest::tableName() . '.technician_id' => $user->id],
                [RepairRequest::tableName() . '.type' => RepairRequest::TYPE_SCHEDULED],
                [RepairRequest::tableName() . '.status' => [RepairRequest::STATUS_COMPLETED, RepairRequest::STATUS_COMPLETED]],
                [
                    'OR',
                    ["like", RepairRequest::tableName() . '.id', $query],
                    ["like", Equipment::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.name', $query],
                ],
                ['>=', RepairRequest::tableName() . '.departed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            ->indexBy("id")
            //            ->createCommand()->rawSql;
            //->limit(20)
            ->orderBy(['departed_at' => SORT_DESC])
            ->all();

        $maintenances = Maintenance::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',
                [Maintenance::tableName() . '.technician_id' => $user->id],
                [Maintenance::tableName() . '.status' => Maintenance::STATUS_COMPLETE],
                [Maintenance::tableName() . '.report_generated' => true],
                [
                    'OR',
                    ["like", Maintenance::tableName() . '.id', $query],
                    ["like", Equipment::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.code', $query],
                    ["like", Location::tableName() . '.name', $query],
                ],
                ['>=', Maintenance::tableName() . '.completed_at', date("Y-m-d H:i:s", strtotime("-3 months"))]
            ])
            ->indexBy("id")
            //            ->createCommand()->rawSql;
            //->limit(20)
            ->orderBy(['completed_at' => SORT_DESC])
            ->all();

        return [
            'repairs' => $repairs,
            'works' => $works,
            'maintenances' => $maintenances,
        ];
    }

    public function actionAcceptTeam($id)
    {
        $this->isPost();

        $request = RepairRequest::findOne($id);
        $user = Assignee::find()->where(['repair_request_id' => $request->id, 'user_id' => Yii::$app->user->id])->one();

        $technicians = \Yii::$app->getRequest()->post("technicians");

        if (!empty($technicians)) {

            $assignees_ids = ArrayHelper::getColumn($request->assignees, 'user_id');
            $out = array_diff($assignees_ids, $technicians);

            foreach ($technicians as $tech) {
                $technician = Assignee::find()->where(['user_id' => $tech, 'repair_request_id' => $request->id])->one();
                $technician->status = Assignee::STATUS_ON_ROAD;
                if ($technician->save()) {
                    if ($technician->user_id != Yii::$app->user->id) {
                        Log::AddLog($technician->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $technician->user->name . " Was Accepted", Assignee::STATUS_ACCEPTED);
                        Log::AddLog($technician->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $technician->user->name . " Was Accepted", Assignee::STATUS_ON_ROAD);
                    }
                }
            }

            foreach ($out as $id) {
                if ($id != Yii::$app->user->id) {
                    $request->checkNotificationEmergency($request->urgent_status, $id, "Technician Acceptance Request", "You Need To Accept Your Assignment To Service #{$request->id}");
                }
            }
        } else {
            $assignees_ids = ArrayHelper::getColumn($request->assignees, 'user_id');

            if (!empty($assignees_ids)) {
                foreach ($assignees_ids as $id) {
                    if ($id != Yii::$app->user->id) {
                        $request->checkNotificationEmergency($request->urgent_status, $id, "Technician Acceptance Request", "You Need To Accept Your Assignment To Service #{$request->id}");
                    }
                }
            }
        }

        $user->status = Assignee::STATUS_ACCEPTED;
        if ($user->save()) {
            Log::AddLog($user->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $user->user->name . " Was Accepted", Assignee::STATUS_ACCEPTED);
        }

        $request->status = RepairRequest::STATUS_CREATED;
        if ($request->save()) {
            Log::AddLog(null, $request->id, Log::TYPE_REPAIR_REQUEST, "Work Order Approval", "Work Order Was Approved By: " . Yii::$app->user->identity->name, $request->status);
        }
        $request->refresh();

        return $request;
    }

    public function actionAcceptTeamMember($id)
    {
        $this->isPost();

        $request = RepairRequest::findOne($id);
        $user = Assignee::find()->where(['repair_request_id' => $request->id, 'user_id' => \Yii::$app->getRequest()->post("technician_id")])->one();

        if (!empty($user)) {
            $user->status = Assignee::STATUS_ACCEPTED;
            if ($user->save()) {
                Log::AddLog($user->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $user->user->name . " Was Accepted", Assignee::STATUS_ACCEPTED);
            }
        }

        $request->refresh();
        return $request;
    }

    public function actionOndate($date, $technician_id = null)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = null;

        if (!empty($technician_id)) {
            $user = Technician::findOne($technician_id);
        } else {
            $user = \Yii::$app->getUser()->getIdentity();
        }

        return [
            'pending_services' => RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
                ->where([Assignee::tableName() . '.user_id' => $user->id])
                ->andWhere([RepairRequest::tableName() . '.division_id' => $user->division_id])
                ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs
                ->andWhere([
                    'AND',
                    ['>=', 'scheduled_at', "{$date} 00:00:00"],
                    ['<=', 'scheduled_at', "{$date} 23:59:59"],
                    [
                        RepairRequest::tableName() . '.status' => [
                            RepairRequest::STATUS_DRAFT,
                            RepairRequest::STATUS_CREATED,
                            RepairRequest::STATUS_CHECKED_IN,
                            RepairRequest::STATUS_ON_HOLD,
                            RepairRequest::STATUS_NOT_COMPLETED,
                            RepairRequest::STATUS_UNABLE_TO_ACCESS,
                            RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,

                        ]
                    ]
                ])
                ->all(),
            'completed_services' => RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
                ->where([Assignee::tableName() . '.user_id' => $user->id])
                ->andWhere([
                    'AND',
                    ['>=', 'departed_at', "{$date} 00:00:00"],
                    ['<=', 'departed_at', "{$date} 23:59:59"],
                    [
                        RepairRequest::tableName() . '.status' => [
                            RepairRequest::STATUS_COMPLETED,
                            RepairRequest::STATUS_REQUEST_COMPLETION,

                        ]
                    ]
                ])
                ->all()
        ];
    }

    public function actionRequestDetails($id)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $request = RepairRequest::findOne($id);

        return $request;

    }

    public function actionChangeEquipment($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $model RepairRequest */
        $model = $user->getRepairRequests()
            ->where(['id' => $id])
            ->one();

        $newEquipmentId = \Yii::$app->getRequest()->post("equipment_id");

        if (!empty($model)) {
            $model->equipment_id = $newEquipmentId;
            $model->save();
            $model->log("Changed equipment to '{$model->equipment->name}'");
        }
        return $model;
    }

    public function actionConfirmEquipment($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $model RepairRequest */
        $model = $user->getRepairRequests()
            ->where(['id' => $id])
            ->one();

        if (!empty($model)) {

            $barcode = \Yii::$app->getRequest()->post("barcode");
            if ($model->equipment->location->is_restricted || empty($barcode)) {
                $equipment_id = \Yii::$app->getRequest()->post("equipment_id");
                $equipment = Equipment::findOne($equipment_id);
                if (!empty($equipment)) {
                    if ($model->equipment->location_id == $equipment->location_id) {
                        //$isFollowupRequest = !empty($model->related_request_id);
                        $model->equipment_id = $equipment->id;
                        $model->confirmed_equipment = true;
                        $model->save();
                        $model->log("Confirmed equipment '{$model->equipment->name}'");
                        return $model;
                    }
                }
                throw new ServerErrorHttpException("Trying to confirm and invalid equipment");
            } else {
                $barcode = \Yii::$app->getRequest()->post("barcode");
                $request = \Yii::$app->getRequest()->post("request");
                $equipmentMaintenanceBarcode = EquipmentMaintenanceBarcode::find()->where(['barcode' => $barcode])->one();

                if (!empty($equipmentMaintenanceBarcode)) {
                    //                if($model->type == RepairRequest::TYPE_REQUEST) {
                    $canRequestSwitch = false;
                    $switchLocation = false;
                    if ($model->equipment->location_id == $equipmentMaintenanceBarcode->equipment->location_id) {
                        $canRequestSwitch = true;
                    } else {
                        //Its not same location, lets check if it is still nearby
                        $nearbyDistance = Setting::getValue("nearby_distance");
                        $distance = Distance::haversineGreatCircleDistance(
                            $model->equipment->location->latitude,
                            $model->equipment->location->longitude,
                            $equipmentMaintenanceBarcode->equipment->location->latitude,
                            $equipmentMaintenanceBarcode->equipment->location->longitude
                        );
                        if ($distance <= $nearbyDistance) {
                            $canRequestSwitch = true;
                            $switchLocation = true;
                        }
                    }
                    //                    if ($model->equipment_id == $equipmentMaintenanceBarcode->equipment_id) {
                    //                        $model->confirmed_equipment = true;
                    //                        $model->save();
                    //                        return $model;
                    //                    }
                    if ($canRequestSwitch) {
                        $equipment = $equipmentMaintenanceBarcode->equipment;
                        $isFollowupRequest = !empty($model->related_request_id);
                        if ($equipment->canRequestRepair() && !$switchLocation) {
                            if ($request == 'yes') {
                                $model->equipment_id = $equipmentMaintenanceBarcode->equipment_id;
                                $model->confirmed_equipment = true;
                                $model->save();
                                $model->log("Confirmed equipment '{$model->equipment->name}'");
                                return $model;
                            } else {
                                if ($model->equipment_id == $equipmentMaintenanceBarcode->equipment_id) {
                                    $model->log("Scanned barcode '{$barcode}' '{$equipment->name}': same equipment");
                                    return [
                                        'need_permission' => true,
                                        'msg' => "Are you sure you want to proceed with equipment '{$equipmentMaintenanceBarcode->equipment->name}'?"
                                    ];
                                } else {
                                    if ($isFollowupRequest) {
                                        $model->log("Scanned barcode '{$barcode}' '{$equipment->name}': different equipment (Not allowed for followup))");
                                        throw new ServerErrorHttpException("You are not allowed to switch to this equipment '{$equipmentMaintenanceBarcode->equipment->name}'");
                                    }
                                    $model->log("Scanned barcode '{$barcode}' '{$equipment->name}': different equipment");
                                    return [
                                        'need_permission' => true,
                                        'msg' => "Are you sure you want to switch to equipment '{$equipment->name}'?"
                                    ];
                                }
                            }
                        } else {
                            if ($request == 'yes') {
                                $model->pending_equipment_id = $equipmentMaintenanceBarcode->equipment_id;
                                $model->confirmed_equipment = false;
                                $model->save();
                                $model->log("Requested equipment switch to '{$equipment->name}'");
                                Notification::notifyAdmins(
                                    "Technician requesting to repair out of maintenance equipment",
                                    "Equipment switch request",
                                    [],
                                    ['/repair-request/view', 'id' => $model->id],
                                    [],
                                    Notification::TYPE_NOTIFICATION,
                                    $model->id
                                );
                                return $model;
                            } else {
                                if ($model->equipment_id != $equipmentMaintenanceBarcode->equipment_id) {
                                    if ($isFollowupRequest) {
                                        $model->log("Scanned barcode '{$barcode}' '{$equipment->name}' Out of maintenance + different equipment (Not allowed for followup))");
                                        throw new ServerErrorHttpException("You are not allowed to switch to this equipment '{$equipmentMaintenanceBarcode->equipment->name}'");
                                    }
                                }
                                if ($switchLocation) {
                                    $model->log("Scanned barcode '{$barcode}' '{$equipment->name}' in '{$equipment->location->name}': different location & equipment");
                                    return [
                                        'need_permission' => true,
                                        'msg' => "Are you sure you want to switch to the new location {$equipment->location->code} - {$equipment->location->name} and equipment {$equipment->name}?"
                                    ];
                                } else {
                                    $model->log("Scanned barcode '{$barcode}' '{$equipment->name}' Out of maintenance");
                                    return [
                                        'need_permission' => true,
                                        'msg' => 'This equipment is out of maintenance, would you like to request approval to proceed with the repair?'
                                    ];
                                }
                            }
                        }
                    }
                    //                }

                    //                if($model->type == RepairRequest::TYPE_SCHEDULED) {
                    //                    if ($model->equipment_id == $equipmentMaintenanceBarcode->equipment_id) {
                    //                        $model->confirmed_equipment = true;
                    //                        $model->save();
                    //                    }
                    //                }
                }
                throw new ServerErrorHttpException("The scanned barcode does not belong to an equipment in the same location nor in a nearby location");
            }
        }
        return $model;
    }



    public function actionAssignWorker($id)
    {
        $this->isPost();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $model RepairRequest */
        $model = $user->getRepairRequests()
            ->where(['id' => $id])
            ->one();

        if (!empty($model)) {
            $model->worker_id = Yii::$app->request->post('worker_id');
            $model->save();
            $model->refresh();
            if (!empty($model->worker)) {
                $model->log("Assigned Worker: {$model->worker->name}");
            }
        }
        return $model;
    }

    public function actionMaintenanceTasks($id)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $repairRequest RepairRequest */
        $repairRequest = $user->getRepairRequests()
            ->where(['id' => $id])
            ->one();

        return MaintenanceTaskGroup::find()
            ->where([
                'AND',
                ['status' => MaintenanceTaskGroup::STATUS_ENABLED],
                ['equipment_type' => $repairRequest->equipment->type]
            ])
            ->orderBy(['group_order' => SORT_ASC])
            ->all();
    }

    public function actionMaintenanceBarcodes($id)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        /* @var $repairRequest RepairRequest */
        $repairRequest = $user->getRepairRequests()
            ->where(['id' => $id])
            ->one();

        $barcodes = EquipmentMaintenanceBarcode::find()
            ->where(['equipment_id' => $repairRequest->equipment_id])
            ->asArray()
            ->all();
        $completed = ArrayHelper::getColumn(CompletedMaintenanceTask::find()
            ->select(['equipment_maintenance_barcode_id'])
            ->where(['repair_request_id' => $id])
            ->asArray()
            ->all(), 'equipment_maintenance_barcode_id', false);
        //return $barcodes;
        return [
            "barcodes" => $barcodes,
            "completed" => $completed
        ];
    }

    public function actionCompleteMaintenanceTask($id, $task_id)
    {
        $this->isGet();

        /* @var $user User */
        $user = \Yii::$app->getUser()->getIdentity();

        $task = CompletedMaintenanceTask::find()
            ->where([
                'AND',
                ['repair_request_id' => $id],
                ['equipment_maintenance_barcode_id' => $task_id]
            ])
            ->one();
        if (empty($task)) {
            $task = new CompletedMaintenanceTask();
            $task->repair_request_id = $id;
            $task->equipment_maintenance_barcode_id = $task_id;
            $task->save();
        }
        return $task;
    }

    // public function actionRequest()
    // {
    //     $this->isPost();
    //     /* @var $user Technician */
    //     $user = Yii::$app->user->identity;
    //     $repairRequest = new RepairRequest();
    //     $repairRequest->reported_by_name = "Technician: " . $user->name;
    //     $repairRequest->reported_by_phone = $user->phone_number;
    //     $repairRequest->type = RepairRequest::TYPE_REQUEST;
    //     $repairRequest->status = RepairRequest::STATUS_DRAFT;
    //     $repairRequest->requested_at = gmdate("Y-m-d H:i:s"); //new Expression("now()");
    //     if ($repairRequest->load(\Yii::$app->getRequest()->post(), "")) {

    //         if ($repairRequest->problem_id == -99) {
    //             $repairRequest->type = RepairRequest::TYPE_SCHEDULED;
    //             $repairRequest->problem_id = null;
    //         } else if ($repairRequest->problem_id == -1) {
    //             $repairRequest->problem_id = null;
    //             if (empty($repairRequest->problem_input)) {
    //                 $repairRequest->problem_id = -1;
    //             }
    //         }


    //         $repairRequest->technician_id = $user->id;
    //         $repairRequest->status = RepairRequest::STATUS_CREATED;
    //         $repairRequest->assigned_at = new Expression("now()");

    //         if ($repairRequest->schedule == RepairRequest::SCHEDULE_RIGHT_NOW) {
    //             $repairRequest->scheduled_at = gmdate('Y-m-d H:i:s');
    //         }

    //         $prevRepair = RepairRequest::find()
    //             ->where([
    //                 'AND',
    //                 ['created_by' => Yii::$app->user->id],
    //                 ['>=', 'created_at', gmdate("Y-m-d H:i:s", strtotime("-10 minutes"))],
    //                 ['equipment_id' => $repairRequest->equipment_id]
    //             ])
    //             ->one();
    //         if (!empty($prevRepair)) {
    //             return $prevRepair;
    //         }


    //         if ($repairRequest->save()) {
    //             $repairRequest->refresh();

    //             $repairRequest->log("Auto Assigned service to {$repairRequest->technician->name}");
    //             if ($repairRequest->type == RepairRequest::TYPE_SCHEDULED) {
    //                 Notification::notifyTechnician(
    //                     $user->id,
    //                     "New works assigned to you",
    //                     "Works #{$repairRequest->id} assigned to you",
    //                     [],
    //                     ['/site/index'],
    //                     ['action' => 'view-service', 'id' => $repairRequest->id],
    //                     null,
    //                     Notification::TYPE_NOTIFICATION,
    //                     $repairRequest->id
    //                 );
    //             }

    //             //                Notification::notifyAdmins("You received a new request #{$repairRequest->id}", "New request received", []
    //             //                    , ['/repair-request/view', 'id' => $repairRequest->id], [], Notification::TYPE_NOTIFICATION, $repairRequest->id);
    //             return $repairRequest;
    //         } else {
    //             $firstErrors = $repairRequest->getFirstErrors();
    //             throw new BadRequestHttpException(array_values($firstErrors)[0]);
    //         }
    //     } else {
    //         throw new FailedToLoadDataException();
    //     }
    // }


    // public function actionRequestForm_old($query)
    // {
    //     $this->isGet();
    //     /* @var $user Technician */
    //     $user = Yii::$app->user->identity;
    //     $sectorsIds = ArrayHelper::getColumn($user->technicianSectors, 'sector_id', false);
    //     if (strlen($query) >= 3) {
    //         return [
    //             'locations' => Location::find()
    //                 ->where([
    //                     'AND',
    //                     ['status' => Location::STATUS_ENABLED],
    //                     [
    //                         'OR',
    //                         ['LIKE', 'code', $query],
    //                         ['LIKE', 'name', $query]
    //                     ],
    //                     ['sector_id' => $sectorsIds]
    //                 ])
    //                 ->indexBy("id")
    //                 ->all(),
    //             'problems' => Problem::findEnabled()->indexBy("id")->all()
    //         ];
    //     }
    //     return [
    //         'locations' => [],
    //         'problems' => [],
    //     ];
    // }
    public function actionRequestForm($query)
    {
        $this->isGet();

        LocationEquipments::$return_fields = LocationEquipments::CASE_MOBILE;
        /* @var $user Technician */
        $user = Yii::$app->user->identity;
        if (strlen($query) >= 3) {
            return [
                'locations' => LocationEquipments::find()
                    ->joinWith('equipmentCaValues')
                    ->where([
                        'AND',
                        // [LocationEquipments::tableName() . '.status' => LocationEquipments::STATUS_ENABLED],
                        [
                            'OR',
                            ['LIKE', LocationEquipments::tableName() . '.code', $query],
                            ['LIKE', LocationEquipments::tableName() . '.value', $query],
                            ['LIKE', EquipmentCaValue::tableName() . '.value', $query]
                        ],
                        [LocationEquipments::tableName() . '.division_id' => Division::DIVISION_PLANT]
                    ])
                    ->indexBy("id")
                    ->all(),
            ];
        }

        return [
            'locations' => [],
        ];
    }

    public function actionCreateService()
    {
        $this->isPost();

        $post = Yii::$app->request->post('data');

        if (!empty($post)) {
            $equipment_id = $post['id'];
            $equipment = LocationEquipments::findOne($equipment_id);

            $remark = $post['remarks'];
            $reported_by_name = $post['reported_by_name'];
            $reported_by_phone = $post['reported_by_phone'];

            $fuel_type = $post['motor_fuel_type'];
            $meter_value = $post['meter_value'];
            $work_type = $post['work_type'];
            $complaints = $post['complaints'];

            $meter_damaged = 0;

            if (!empty($post['meter_damaged'])) {
                $meter_damaged = 1;
            }
            $chassie_number = $post['chassie_number'];

            $model = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_CRONJOB]);
            $model->service_type = $work_type;
            $model->status = RepairRequest::STATUS_DRAFT;
            $model->equipment_id = $equipment->id;
            $model->category_id = $equipment->equipment->category_id;
            $model->location_id = $equipment->location_id;
            $model->division_id = $equipment->division_id;
            $model->sector_id = $equipment->location->sector_id;
            $model->reported_by_name = $reported_by_name;
            $model->reported_by_phone = $reported_by_phone;
            $model->service_note = $remark;
            $model->need_review = false;
            $model->repair_request_path = @Equipment::getLayersValueTextInput($equipment->value, ",\n");
            $model->scheduled_at = gmdate("Y-m-d H:i:s");
            $model->urgent_status = false;
            $model->coordinator_note = $complaints;
            if ($model->Save()) {
                $model->checkMissingAssigneesWithoutStatus([Yii::$app->user->id], false);

                $admin_notification_model = new AdminNotifications();
                $admin_notification_model->request_id = $model->id;
                $admin_notification_model->technician_id = Yii::$app->user->id;
                $admin_notification_model->seen = false;
                $admin_notification_model->type = AdminNotifications::TYPE_STATUS;
                $admin_notification_model->status = $model->status;
                $admin_notification_model->save(false);

                $model->CheckPlantPpmTasks($model->equipment_id);
                $model->CheckPlantChecklistTasks($model->equipment_id);
            }

            $equipment->meter_value = $meter_value;
            $equipment->meter_damaged = $meter_damaged;
            $equipment->chassie_number = $chassie_number;
            $equipment->motor_fuel_type = $fuel_type;

            if ($equipment->save()) {
                return true;
            } else {
                print_r($equipment->errors);
                exit;
            }
        }

        return false;
    }

    public function actionAvailableWorkers()
    {
        $this->isGet();
        /* @var $user Technician */
        $user = Yii::$app->user->identity;
        $sectorsIds = ArrayHelper::getColumn($user->technicianSectors, 'sector_id', false);
        return Worker::find()
            ->joinWith(['workerSectors'])
            ->where([
                'AND',
                [Worker::tableName() . '.status' => Worker::STATUS_ENABLED],
                [WorkerSector::tableName() . '.sector_id' => $sectorsIds]
            ])
            //->indexBy(Worker::tableName() . ".id")
            ->all();
    }
}
