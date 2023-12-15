<?php

namespace admin\controllers;

use common\components\helpers\ImageUploadHelper;
use common\components\notification\Notification;
use common\config\includes\P;
use common\models\Assignee;
use common\models\Division;
use common\models\Equipment;
use common\models\Gallery;
use common\models\Image;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Log;
use yii\helpers\FileHelper;
use common\models\PlantPpmTasksHistory;
use common\models\RepairRequest;
use common\models\RepairRequestChats;
use common\models\RepairRequestFiles;
use common\models\search\RepairRequestSearch;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Technician;
use Imagick;
use Yii;
use yii\base\DynamicModel;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * RepairRequestController implements the CRUD actions for RepairRequest model.
 */
class RepairRequestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW) || P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW),
                        'actions' => ['index', 'departed', 'completed', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_DEPARTED_SERVICES_UPDATE)
                            || P::c(P::REPAIR_REPAIR_DASHBOARD_ONGOING_SERVICES_UPDATE)
                            || P::c(P::REPAIR_REPAIR_DASHBOARD_UPCOMING_DAYS_SERVICES_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PENDING_SERVICES_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_NEW) || P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_NEW),
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PENDING_SERVICES_APPROVE_ASSIGN),
                        'actions' => ['assign'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PENDING_SERVICES_RE_SCHEDULE)
                            || P::c(P::REPAIR_REPAIR_DASHBOARD_UPCOMING_DAYS_SERVICES_RE_SCHEDULE),
                        'actions' => ['reschedule'],
                        'roles' => ['@'],
                    ],

                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_DEPARTED_SERVICES_COMPLETE_SERVICE),
                        'actions' => ['complete', 'complete-duplicate', 'create-followup'],
                        'roles' => ['@'],
                    ],

                    [
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_ONGOING_SERVICES_UPDATE),
                        'actions' => ['mark-non-operational', 'mark-operational', 'mark-non-trapped', 'mark-trapped', 'mark-non-complete', 'mark-complete', 'save-atl-note', 'save-client-note'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['reopen-request'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['attach-files', 'download'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['import-maintenance'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['equipment-path', 'tasks', 'location-division-main-sector', 'category-equipment-type', 'change-team', 'change-status', 'add-plant-ppm-tasks', 'chats', 'send-message', 'set-labor-charge'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::REPAIR_COMPLETED_REPAIRS_PAGE_VIEW_REPORT),
                        'actions' => ['print-report', 'view-report', 'view-tif', 'test-report'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RepairRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RepairRequestSearch();

        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all RepairRequest models.
     * @return mixed
     */
    public function actionDeparted()
    {
        $searchModel = new RepairRequestSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all RepairRequest models.
     * @return mixed
     */
    public function actionCompleted()
    {
        $searchModel = new RepairRequestSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RepairRequest model.
     * @param integer $id
     * @return mixed
     */

    public function actionAttachFiles($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->fileUpload = UploadedFile::getInstances($model, 'fileUpload');
            $fileCount = RepairRequestFiles::find()->where(['repair_request_id' => $model->id])->count();

            foreach ($model->fileUpload as $index => $file) {
                $uploadPath = Yii::getAlias('@static') . '/upload/repairRequestFiles';
                FileHelper::createDirectory($uploadPath);
                $filename = 'repair_request_' . $model->id . '(' . ($fileCount + $index + 1) . ').' . $file->extension;
                if ($file->saveAs($uploadPath . '/' . $filename)) {
                    $newFileModel = new RepairRequestFiles();
                    $newFileModel->repair_request_id = $model->id;
                    $newFileModel->old_file = $file;
                    $newFileModel->new_file = $filename;
                    $newFileModel->type = $file->extension;
                    $newFileModel->save();
                    Yii::$app->session->setFlash('success', 'Files successfully uploaded.');
                } else {
                    print_r($newFileModel->getErrors());
                    exit;
                    Yii::$app->session->setFlash('error', 'Files upload failed.');
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionDownload($id)
    {
        $repairFile = RepairRequestFiles::findOne($id);

        if ($repairFile) {
            $file = $repairFile->new_file;
            $filePath = Yii::getAlias('@static') . '/upload/repairRequestFiles/' . $file;
            if (file_exists($filePath)) {
                Yii::$app->response->sendFile($filePath, $file)->send();
            } else {
                echo $filePath . ' File not found.';
            }
        } else {
            echo 'Repair file not found.';
        }
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the RepairRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RepairRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = RepairRequest::find()->where(['id' => $id]);

        if (!empty(Yii::$app->user->identity->division_id)) {
            $query->andFilterWhere(['division_id' => Yii::$app->user->identity->division_id]);
        }
        if (($model = $query->one()) !== null) {
            //        if (($model = RepairRequest::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new RepairRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreateOld()
    // {
    //     $model = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_ADMIN]);

    //     if ($model->load(Yii::$app->request->post())) {
    //         $model->requested_at = gmdate("Y-m-d H:i:s", strtotime($model->requested_at));
    //         $model->scheduled_at = gmdate("Y-m-d H:i:s", strtotime($model->scheduled_at));

    //         if ($model->problem_id == -1) {
    //             $model->problem_id = null;
    //             if (empty($model->problem_input)) {
    //                 $model->problem_id = -1;
    //             }
    //         }

    //         $prevRepair = RepairRequest::find()
    //             ->where([
    //                 'AND',
    //                 ['created_by' => Yii::$app->user->id],
    //                 ['>=', 'created_at', gmdate("Y-m-d H:i:s", strtotime("-10 minutes"))],
    //                 ['equipment_id' => $model->equipment_id]
    //             ])
    //             ->one();
    //         if (!empty($prevRepair)) {
    //             return $this->redirect(['view', 'id' => $prevRepair->id]);
    //         }

    //         if ($model->save()) {
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //     }
    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionCreate()
    {
        $model = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_ADMIN]);

        $status_require_change = false;

        if ($model->load(Yii::$app->request->post())) {

            $post = Yii::$app->request->post('RepairRequest');

            $status = @$post['status'];

            if (empty($model->owner_id) && empty($model->team_leader_id)) {
                Yii::$app->session->setFlash('error', 'Select Supervisor Or Team Leader!!');
                return $this->render('create', [
                    'model' => $model
                ]);
            }

            if (empty($post['owner_id'])) {
                $model->owner_id = @$post['team_leader_id'];
            }

            $model->location_id = @Location::find()->where(['code' => @$post['location_id']])->one()->id;

            $equipment_code = @$post['equipment_id'];

            $model->equipment_id = @LocationEquipments::find()->where(['code' => $equipment_code, 'location_id' => $model->location_id])->one()->id;

            // if (empty($model->location_id)) {
            //     Yii::$app->session->setFlash('error', 'Both Location And Equipment are required to create an order!!');
            //     return $this->redirect(['create']);
            // }

            $need_review = $post['need_review'];

            if (!isset($status)) {
                if (!empty($need_review) && $need_review) {
                    $model->status = RepairRequest::STATUS_DRAFT;
                } else {
                    $model->status = RepairRequest::STATUS_CREATED;
                }
            } else {
                $status_require_change = true;
            }


            $technicians = $post['technician_id'];

            if ($model->save()) {

                Log::AddLog(null, $model->id, Log::TYPE_REPAIR_REQUEST, "Work Order Creation", "Work Order #{$model->id} Was Created", $model->status);

                if ($status_require_change) {
                    $model->status = $status;

                    $statuses = (new RepairRequest())->status_list;
                    Log::AddLog(null, $model->id, Log::TYPE_REPAIR_REQUEST, "Work Order Status", "Work Status Changed To: " . $statuses[$status], $status);
                }

                $model->requested_at = gmdate("Y-m-d H:i:s");
                // if (!empty($model->owner_id)) {
                //     $model->checkNotificationEmergency($model->urgent_status, $model->owner_id , "Service assigned to you", "New Work OrderService #{$model->id} assigned to you");
                // }

                Yii::$app->session->setFlash('success', 'Work Order Created Successfully!!');

                if (!empty($technicians)) {

                    $model->assigned_at = gmdate("Y-m-d H:i:s");

                    $model->checkMissingAssigneesWithoutStatus($technicians, isset($status) ? false : true);

                    // foreach ($technicians as $technician) {
                    //     $assignee_model = new Assignee();
                    //     $assignee_model->repair_request_id = $model->id;
                    //     $assignee_model->user_id = $technician;
                    //     $assignee_model->datetime = $model->scheduled_at;
                    //     $assignee_model->status = Assignee::STATUS_ASSIGNED;
                    //     if ($assignee_model->save()) {
                    //         $model->checkNotificationEmergency($model->urgent_status, $technician, "Service assigned to you", "New Work OrderService #{$model->id} assigned to you");
                    //     }
                    // }

                    $model->informed_at = gmdate("Y-m-d H:i:s");
                }

                if (!empty($post['owner_id'])) {
                    $assignee_model = new Assignee();
                    $assignee_model->repair_request_id = $model->id;
                    $assignee_model->user_id = $post['owner_id'];
                    $assignee_model->datetime = $model->scheduled_at;
                    $assignee_model->status = Assignee::STATUS_ASSIGNED;
                    if ($assignee_model->save()) {
                        Log::AddLog($assignee_model->user_id, $model->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $assignee_model->user->name . " Was Assigned", Assignee::STATUS_ASSIGNED);

                        if (!isset($status))
                            $model->checkNotificationEmergency($model->urgent_status, $post['owner_id'], "Service #{$model->id} assigned to you", "{$model->location->name} | {$model->equipment->equipment->name} - {$model->equipment->code}");
                    }
                }

                $model->refresh();

                if ($model->division_id == Division::DIVISION_MALL) {
                    $model->createMallPpmTask();
                } else if ($model->division_id == Division::DIVISION_PLANT) {
                    $model->CheckPlantPpmTasks($model->equipment_id);
                    $model->CheckPlantChecklistTasks($model->equipment_id);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionEquipmentPath()
    {
        if (Yii::$app->request->post()) {
            $equipment_id = Yii::$app->request->post('equipment_id');
            $is_location = Yii::$app->request->post('is_location');
            $order_id = Yii::$app->request->post('order_id');
            $response = [];

            $equipment_path = '';

            if (!empty($is_location) && $is_location) {
                $sector = Sector::find()->where(['id' => $equipment_id])->one();

                $equipment_path = SegmentPath::getLayersValue(@SegmentPath::find()->where(['sector_id' => $sector->id])->one()->value, ",\n", true);
            } else {
                $equipment_path = @Equipment::getLayersValueTextInput(@LocationEquipments::findOne(['code' => $equipment_id])->value, ",\n");
            }

            if (!empty($equipment_path)) {
                $response = ['response' => $equipment_path];
            } else {
                $response = ['response' => null];
            }

            return \yii\helpers\Json::encode($response);
        }

        throw new \yii\web\BadRequestHttpException("Error");
    }

    public function actionLocationDivisionMainSector()
    {
        if (Yii::$app->request->post()) {
            $location = @Location::findOne(['code' => Yii::$app->request->post('location_id')]);
            $response = '';


            $division_main_sector = @Division::findOne($location->division_id)->name . ' | ' . @$location->sector->mainSector->name . ' | ' . @$location->sector->name;

            if (!empty($location)) {
                $response = ['response' => $division_main_sector, 'division_id' => $location->division_id];
            } else {
                $response = ['response' => null];
            }

            return \yii\helpers\Json::encode($response);
        }

        throw new \yii\web\BadRequestHttpException("Error");
    }

    public function actionCategoryEquipmentType()
    {
        if (Yii::$app->request->post()) {
            $location_equipment = @LocationEquipments::find()->where(['code' => Yii::$app->request->post('equipment_id')])->one();
            $response = '';

            $result = @$location_equipment->equipment->category->name . ' | ' . @$location_equipment->equipment->equipmentType->name;

            if (!empty($result)) {
                $response = ['response' => $result, 'category_id' => $location_equipment->equipment->category->id];
            } else {
                $response = ['response' => null];
            }

            return \yii\helpers\Json::encode($response);
        }

        throw new \yii\web\BadRequestHttpException("Error");
    }

    /**
     * Updates an existing RepairRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdateOld($id)
    // {
    //     $model = $this->findModel($id);
    //     if ($model->load(Yii::$app->request->post())) {

    //         if ($model->problem_id == -1) {
    //             $model->problem_id = null;
    //             if (empty($model->problem_input)) {
    //                 $model->problem_id = -1;
    //             }
    //         }


    //         $oldModel = $this->findModel($id);
    //         $dirtyAttributes = $model->getDirtyAttributes();
    //         $whatChanged = [];
    //         foreach ($dirtyAttributes as $dirtyAttribute => $value) {
    //             $oldValue = $oldModel->{$dirtyAttribute};
    //             $newValue = $model->{$dirtyAttribute};
    //             if ($oldModel->{$dirtyAttribute} != $model->{$dirtyAttribute}) {
    //                 $label = "{$dirtyAttribute}_label";
    //                 if (!empty($model->{$label})) {
    //                     $newValue = $model->{$label};
    //                 }
    //                 if (!empty($oldModel->{$label})) {
    //                     $oldValue = $oldModel->{$label};
    //                 }
    //                 if (str_ends_with($dirtyAttribute, "_id")) {
    //                     $withoutId = str_replace("_id", "", $dirtyAttribute);
    //                     if (!empty($model->{$withoutId})) {
    //                         if (!empty($model->{$withoutId}->name)) {
    //                             $newValue = $model->{$withoutId}->name;
    //                         } else if (!empty($model->{$withoutId}->code)) {
    //                             $newValue = $model->{$withoutId}->code;
    //                         }
    //                     }
    //                     if (!empty($oldModel->{$withoutId})) {
    //                         if (!empty($oldModel->{$withoutId}->name)) {
    //                             $oldValue = $oldModel->{$withoutId}->name;
    //                         } else if (!empty($oldModel->{$withoutId}->code)) {
    //                             $oldValue = $oldModel->{$withoutId}->code;
    //                         }
    //                     }
    //                 }
    //                 $whatChanged[] = " - " . $model->getAttributeLabel($dirtyAttribute) .
    //                     " changed from (" . $oldValue . ") To (" . $newValue . ")" . "";
    //             }
    //         }
    //         if ($model->save()) {
    //             if (!empty($whatChanged)) {
    //                 $model->log("Updated from backend:<br/> " . implode("<br/> ", $whatChanged));
    //             }
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //     }
    //     return $this->render('update', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        $model->equipment_id = @$model->equipment->code;
        $model->location_id = @$model->location->code;

        $oldAttributes = $model->getOldAttributes();

        if ($model->load(Yii::$app->request->post())) {

            $newAttributes = $model->getAttributes();

            $changedData = array_diff_assoc($newAttributes, $oldAttributes);

            unset($changedData['equipment_id']);
            unset($changedData['location_id']);

            // print_r($changedData);
            // exit;

            if (count($changedData) > 0) {
                foreach ($changedData as $key => $value) {

                    $out = $value;

                    if ($key == "service_type") {
                        $out = $model->service_type_list[$value];
                    } else if ($key === "urgent_status" || $key === "technician_from_another_division" || $key === "need_review") {
                        $out = ($value == 1 ? "true" : "false");
                    } else if ($key == "owner_id" || $key = "team_leader_id") {
                        $out = @Technician::findOne($value)->name;
                    }
                    Log::AddLog(null, $model->id, Log::TYPE_REPAIR_REQUEST, "Work Order Updates", $model->getAttributeLabel($key) . " was changed to: " . $out, $model->status);
                }
            }

            $post = Yii::$app->request->post('RepairRequest');

            $status = @$post['status'];

            if (empty($model->owner_id) && empty($model->team_leader_id)) {
                Yii::$app->session->setFlash('error', 'Select Supervisor Or Team Leader!!');
                return $this->render('update', [
                    'model' => $model
                ]);
            }

            if (empty($post['owner_id'])) {
                $model->owner_id = @$post['team_leader_id'];
            }

            $model->location_id = @Location::find()->where(['code' => $post['location_id']])->one()->id;

            $equipment_code = $post['equipment_id'];

            $model->equipment_id = $model->location_id ? @LocationEquipments::find()->where(['code' => $equipment_code, 'location_id' => $model->location_id])->one()->id : null;


            $need_review = @$post['need_review'];

            // print_r($post['status']);
            // exit;

            if (empty($post['status'])) {
                if (!empty($need_review) && $need_review) {
                    $model->status = RepairRequest::STATUS_DRAFT;
                } else if ($model->status == RepairRequest::STATUS_DRAFT && $model->division_id == Division::DIVISION_PLANT) {
                    $model->status = RepairRequest::STATUS_CREATED;
                }
            } else {
                $model->status = $status;

                $statuses = (new RepairRequest())->status_list;
                Log::AddLog(null, $model->id, Log::TYPE_REPAIR_REQUEST, "Work Order Status", "Work Status Changed To: " . $statuses[$status], $status);
            }


            $technicians_ids = $post['technician_id'];

            if ($model->save()) {

                Yii::$app->session->setFlash('success', 'Work Order Updated Successfully!!');

                $model->checkMissingAssigneesWithoutStatus($technicians_ids, isset($status) ? false : true);
                // Assignee::deleteAll(['repair_request_id' => $model->id]);

                // if (!empty($technicians)) {
                //     foreach ($technicians as $technician) {
                //         $assignee_model = new Assignee();
                //         $assignee_model->repair_request_id = $model->id;
                //         $assignee_model->user_id = $technician;
                //         $assignee_model->datetime = $model->scheduled_at;
                //         $assignee_model->status = Assignee::STATUS_ASSIGNED;
                //         $assignee_model->save();
                //     }
                // }

                // if (empty($model->owner_id) && !empty($post['owner_id'])) {
                //     $assignee_model = new Assignee();
                //     $assignee_model->repair_request_id = $model->id;
                //     $assignee_model->user_id = $post['owner_id'];
                //     $assignee_model->datetime = $model->scheduled_at;
                //     $assignee_model->status = Assignee::STATUS_ASSIGNED;
                //     $assignee_model->save();
                // }

                if (!empty($post['owner_id'])) {

                    $assignee = Assignee::find()->where(['repair_request_id' => $model->id, 'user_id' => $post['owner_id']])->one();

                    if (empty($assignee)) {
                        $assignee_model = new Assignee();
                        $assignee_model->repair_request_id = $model->id;
                        $assignee_model->user_id = $post['owner_id'];
                        $assignee_model->datetime = $model->scheduled_at;
                        $assignee_model->status = Assignee::STATUS_ASSIGNED;
                        if ($assignee_model->save()) {
                            Log::AddLog($assignee_model->user_id, $model->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $assignee_model->user->name . " Was Assigned", Assignee::STATUS_ASSIGNED);

                            if (!isset($status))
                                $model->checkNotificationEmergency($model->urgent_status, $post['owner_id'], "Service #{$model->id} assigned to you", "{$model->location->name} | {$model->equipment->equipment->name} - {$model->equipment->code}");
                        }
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSaveAtlNote($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->log("Changed Atl Note");
            Yii::$app->session->addFlash("success", "ATL Note Saved");
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionSaveClientNote($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->log("Changed Client Note");
            Yii::$app->session->addFlash("success", "Client Note Saved");
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionImportMaintenance()
    {
        if (Yii::$app->request->isPost) {
            $uploadedFile = UploadedFile::getInstanceByName("import");
            if (!empty($uploadedFile) && $uploadedFile instanceof UploadedFile) {
                $path = Yii::getAlias("@static/upload/importschedule.csv");
                if ($uploadedFile->saveAs($path)) {
                    if (($handle = fopen($path, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
                            $equipmentCode = trim($data[0]);
                            $technicianEmail = trim($data[1]);
                            $date = trim($data[2]);
                            $equipment = Equipment::find()
                                ->where(['code' => $equipmentCode])
                                ->one();
                            if (empty($equipment)) {
                                Yii::$app->session->addFlash("danger", "Equipment {$equipmentCode} not found");
                                continue;
                            }
                            $technician = Technician::find()
                                ->where(['email' => $technicianEmail])
                                ->one();
                            if (empty($technician)) {
                                Yii::$app->session->addFlash("danger", "Technician {$technicianEmail} not found");
                                continue;
                            }
                            $repairRequest = new RepairRequest();
                            $repairRequest->equipment_id = $equipment->id;
                            $repairRequest->technician_id = $technician->id;
                            $repairRequest->status = RepairRequest::STATUS_CREATED;
                            // $repairRequest->type = RepairRequest::TYPE_SCHEDULED;
                            $repairRequest->schedule = RepairRequest::SCHEDULE_SCHEDULED;
                            $repairRequest->requested_at = gmdate("Y-m-d H:i:s");
                            $repairRequest->scheduled_at = gmdate("Y-m-d H:i:s", strtotime($date));
                            if (!$repairRequest->save()) {
                                Yii::$app->session->addFlash("danger", "There was error creating service {$equipmentCode} - {$technicianEmail} - {$date}");
                                Yii::$app->session->addFlash("danger", Json::encode($repairRequest->errors));
                            }
                        }
                        fclose($handle);
                        return $this->redirect(['index']);
                    }
                }
            }
        }

        return $this->render('import-maintenance', []);
    }

    /**
     * Deletes an existing RepairRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // public function actionAssign($id)
    // {
    //     $model = $this->findModel($id);
    //     $technician_id = Yii::$app->request->post("technician_id");
    //     $model->technician_id = $technician_id;
    //     $model->status = RepairRequest::STATUS_CREATED;
    //     $model->last_handled_by = Yii::$app->user->id;
    //     $model->assigned_at = new Expression("now()");
    //     if ($model->save()) {
    //         $model->log("Assigned service to {$model->technician->name}");
    //         Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
    //         if ($model->type == RepairRequest::TYPE_REQUEST) {
    //             if ($model->person_trapped) {
    //                 Notification::notifyTechnician(
    //                     $technician_id,
    //                     "Service assigned to you",
    //                     "<div style='color: #ce222a;font-weight: 700'>Emergency - Trapped Passenger</div>Service #{$id} assigned to you",
    //                     [],
    //                     ['/site/index'],
    //                     ['action' => 'view-service', 'id' => $id],
    //                     null,
    //                     Notification::TYPE_NOTIFICATION,
    //                     $id
    //                 );
    //             } else {
    //                 Notification::notifyTechnician(
    //                     $technician_id,
    //                     "Service assigned to you",
    //                     "Service #{$id} assigned to you",
    //                     [],
    //                     ['/site/index'],
    //                     ['action' => 'view-service', 'id' => $id],
    //                     null,
    //                     Notification::TYPE_NOTIFICATION,
    //                     $id
    //                 );
    //             }
    //             Notification::notifyEquipmentUsers(
    //                 $model->equipment,
    //                 "Service Approved",
    //                 "A technician will be informed and dispatched shortly.",
    //                 [],
    //                 ['/site/index'],
    //                 ['action' => 'view-service', 'id' => $model->id],
    //                 null,
    //                 Notification::TYPE_NOTIFICATION,
    //                 $model->id
    //             );
    //         }
    //         if ($model->type == RepairRequest::TYPE_SCHEDULED) {
    //             Notification::notifyTechnician(
    //                 $technician_id,
    //                 "New works assigned to you",
    //                 "Works #{$id} assigned to you",
    //                 [],
    //                 ['/site/index'],
    //                 ['action' => 'view-service', 'id' => $id],
    //                 null,
    //                 Notification::TYPE_NOTIFICATION,
    //                 $id
    //             );
    //         }

    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('update', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    // public function actionReschedule($id)
    // {
    //     $model = $this->findModel($id);
    //     $previousDate = $model->scheduled_at;
    //     if ($model->load(Yii::$app->request->post())) {
    //         $asSelected = $model->scheduled_at;
    //         $model->scheduled_at = gmdate("Y-m-d H:i:s", strtotime($model->scheduled_at));
    //         if ($previousDate == $model->scheduled_at) {
    //             Yii::$app->session->addFlash("warning", "Same schedule selected");
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //         if (gmdate("Y-m-d H:i:s") > $model->scheduled_at) {
    //             Yii::$app->session->addFlash("danger", "Please select date in the future");
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //         $wasApproved = false;
    //         if ($model->status == RepairRequest::STATUS_INFORMED) {
    //             $wasApproved = true;
    //             $model->status = RepairRequest::STATUS_CREATED;
    //         }
    //         if ($model->save()) {
    //             if ($model->equipment->checkIfExtraCostApplies($model->scheduled_at)) {
    //                 $model->extra_cost = 100;
    //             } else {
    //                 $model->extra_cost = null;
    //             }
    //             $model->save(false);

    //             $model->log("Rescheduled service to {$asSelected}");
    //             Yii::$app->session->addFlash("success", "Successfully rescheduled");
    //             Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
    //             if ($model->status == RepairRequest::STATUS_CREATED) {
    //                 $technician_id = $model->technician_id;
    //                 if ($wasApproved) {
    //                     if ($model->type == RepairRequest::TYPE_REQUEST) {
    //                         Notification::notifyTechnician(
    //                             $technician_id,
    //                             "Service Re-Scheduled",
    //                             "Service #{$id} has been Rescheduled",
    //                             [],
    //                             ['/site/index'],
    //                             ['action' => 'view-service', 'id' => $id],
    //                             null,
    //                             Notification::TYPE_NOTIFICATION,
    //                             $id
    //                         );
    //                     }
    //                     if ($model->type == RepairRequest::TYPE_SCHEDULED) {
    //                         Notification::notifyTechnician(
    //                             $technician_id,
    //                             "Works Re-Scheduled",
    //                             "Works #{$id} has been Rescheduled",
    //                             [],
    //                             ['/site/index'],
    //                             ['action' => 'view-service', 'id' => $id],
    //                             null,
    //                             Notification::TYPE_NOTIFICATION,
    //                             $id
    //                         );
    //                     }
    //                 } else {
    //                     if ($model->type == RepairRequest::TYPE_REQUEST) {
    //                         Notification::notifyTechnician(
    //                             $technician_id,
    //                             "Service assigned to you",
    //                             "Service #{$id} assigned to you",
    //                             [],
    //                             ['/site/index'],
    //                             ['action' => 'view-service', 'id' => $id],
    //                             null,
    //                             Notification::TYPE_NOTIFICATION,
    //                             $id
    //                         );
    //                     }
    //                     if ($model->type == RepairRequest::TYPE_SCHEDULED) {
    //                         Notification::notifyTechnician(
    //                             $technician_id,
    //                             "New works assigned to you",
    //                             "Works #{$id} assigned to you",
    //                             [],
    //                             ['/site/index'],
    //                             ['action' => 'view-service', 'id' => $id],
    //                             null,
    //                             Notification::TYPE_NOTIFICATION,
    //                             $id
    //                         );
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $this->redirect(['view', 'id' => $model->id]);
    // }

    public function actionConfirmPendingEquipment($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->pending_equipment_id)) {
            $model->equipment_id = $model->pending_equipment_id;
            $model->confirmed_equipment = true;
            $model->pending_equipment_id = null;
            if ($model->save()) {
                $model->log("Confirmed equipment switch to service to '{$model->equipment->name}'");
                Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
                Notification::notifyTechnician(
                    $model->technician_id,
                    "Equipment switch approved",
                    "Equipment switch approved",
                    [],
                    ['/site/index'],
                    ['action' => 'view-service', 'id' => $id],
                    null,
                    Notification::TYPE_NOTIFICATION,
                    $id
                );
            }
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionRejectPendingEquipment($id)
    {
        $model = $this->findModel($id);
        $model->pending_equipment_id = null;
        if ($model->save()) {
            $model->log("Rejected equipment switch to service to '{$model->equipment->name}'");
            Notification::deleteAll(['relation_key' => $id]); //Delete all past notifications for this request
            Notification::notifyTechnician(
                $model->technician_id,
                "Equipment switch rejected",
                "Equipment switch rejected",
                [],
                ['/site/index'],
                ['action' => 'view-service', 'id' => $id],
                null,
                Notification::TYPE_NOTIFICATION,
                $id
            );
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }


    // public function actionReopenRequest($id)
    // {
    //     $model = $this->findModel($id);
    //     if ($model->status = RepairRequest::STATUS_COMPLETED) {
    //         $model->status = RepairRequest::STATUS_CHECKED_IN;
    //         Notification::notifyTechnician(
    //             $model->technician_id,
    //             "Service Re-Opened",
    //             "Service #{$id} re-opened to complete maintenance tasks",
    //             [],
    //             ['/site/index'],
    //             ['action' => 'view-service', 'id' => $id],
    //             null,
    //             Notification::TYPE_NOTIFICATION,
    //             $id
    //         );

    //         $model->save();
    //     }

    //     return $this->redirect(['view', 'id' => $model->id]);
    // }

    public function actionComplete($id)
    {
        $model = $this->findModel($id);
        $skipVerification = false;
        if (!empty($model->related_request_id)) {
            $relatedModel = $this->findModel($model->related_request_id);
            if (!empty($relatedModel->notification_id)) {
                $model->notification_id = $relatedModel->notification_id;
                $skipVerification = true;
            }
        }
        if (false && !$skipVerification) {
            $notificationIdModel = new DynamicModel(['notification_id']);
            $notificationIdModel->addRule(['notification_id'], 'safe');
            //$notificationIdModel->addRule(['notification_id'], 'required');
            //$notificationIdModel->addRule(['notification_id'], 'string', ['min' => 10, 'max' => 15]);
            if ($notificationIdModel->load(Yii::$app->request->post(), '')) {
                if (!$notificationIdModel->validate()) {
                    Yii::$app->session->addFlash("danger", "Notification ID is invalid");
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    //Check if it is unique
                    $count = RepairRequest::find()->where(['notification_id' => $notificationIdModel->notification_id])->count();
                    if ($count > 0) {
                        Yii::$app->session->addFlash("danger", "Notification ID must be unique");
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $model->notification_id = $notificationIdModel->notification_id;
                    }
                }
            }
        }
        if ($model->status = RepairRequest::STATUS_COMPLETED) {
            $model->completed_by = Yii::$app->user->id;
            $model->status = RepairRequest::STATUS_COMPLETED;
            $model->completed_at = new Expression("now()");
            $model->save();
            $model->generatePdfReport();
            $model->log("Completed service");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkNonOperational($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->system_operational = false;
            $model->save(false);
            Yii::$app->session->addFlash("danger", "Unit marked as non operational");
            $model->log("Marked unit as non-operational");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkOperational($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->system_operational = true;
            $model->save(false);
            Yii::$app->session->addFlash("success", "Unit marked as operational");
            $model->log("Marked unit as operational");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkNonTrapped($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->person_trapped = false;
            $model->save(false);
            Yii::$app->session->addFlash("danger", "Marked as non passenger was trapped");
            $model->log("Marked as non passenger was trapped");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkTrapped($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->person_trapped = true;
            $model->save(false);
            Yii::$app->session->addFlash("success", "Marked as passenger was trapped");
            $model->log("Marked as passenger was trapped");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkNonComplete($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->works_completed = false;
            $model->save(false);
            Yii::$app->session->addFlash("danger", "Work marked as non completed");
            $model->log("Marked work as non completed");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionMarkComplete($id)
    {
        $model = $this->findModel($id);
        if ($model->status == RepairRequest::STATUS_COMPLETED) {
            $model->works_completed = true;
            $model->save(false);
            Yii::$app->session->addFlash("success", "Work marked as complete");
            $model->log("Marked work as completed");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionCompleteDuplicate($id)
    {
        $model = $this->findModel($id);

        $skipVerification = false;
        if (!empty($model->related_request_id)) {
            $relatedModel = $this->findModel($model->related_request_id);
            if (!empty($relatedModel->notification_id)) {
                $model->notification_id = $relatedModel->notification_id;
                $skipVerification = true;
            }
        }
        if (false && !$skipVerification) {
            $notificationIdModel = new DynamicModel(['notification_id']);
            $notificationIdModel->addRule(['notification_id'], 'safe');
            //            $notificationIdModel->addRule(['notification_id'], 'required');
            //            $notificationIdModel->addRule(['notification_id'], 'string', ['min' => 10, 'max' => 15]);
            if ($notificationIdModel->load(Yii::$app->request->post(), '')) {
                if (!$notificationIdModel->validate()) {
                    Yii::$app->session->addFlash("danger", "Notification ID is invalid");
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    //Check if it is unique
                    $count = RepairRequest::find()->where(['notification_id' => $notificationIdModel->notification_id])->count();
                    if ($count > 0) {
                        Yii::$app->session->addFlash("danger", "Notification ID must be unique");
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $model->notification_id = $notificationIdModel->notification_id;
                    }
                }
            }
        }

        if ($model->status = RepairRequest::STATUS_COMPLETED) {
            $model->status = RepairRequest::STATUS_COMPLETED;
            $model->completed_at = new Expression("now()");
            $model->save();

            $request = new RepairRequest();
            $request->equipment_id = $model->equipment_id;
            $request->requested_at = $model->requested_at;
            $request->user_id = $model->user_id;
            $request->problem_id = $model->problem_id;
            $request->schedule = RepairRequest::SCHEDULE_SCHEDULED;
            $request->scheduled_at = gmdate("Y-m-d H:i:s", strtotime(date("Y-m-d 08:00:00") . " +1 day"));
            $request->type = $model->type;
            $request->related_request_id = $model->id;
            if ($request->save()) {
                $model->generatePdfReport();
                $model->log("Completed service + create new one");
                Yii::$app->session->addFlash("info", "New request created #{$request->id}");
            }
        }

        return $this->redirect(['update', 'id' => $request->id]);
    }


    public function actionCreateFollowup($id)
    {
        $model = $this->findModel($id);


        $request = new RepairRequest();
        $request->equipment_id = $model->equipment_id;
        $request->requested_at = $model->requested_at;
        $request->user_id = $model->user_id;
        $request->problem_id = $model->problem_id;
        $request->schedule = RepairRequest::SCHEDULE_SCHEDULED;
        $request->scheduled_at = gmdate("Y-m-d H:i:s", strtotime(date("Y-m-d 08:00:00") . " +1 day"));
        $request->type = $model->type;
        $request->related_request_id = $model->id;
        if ($request->save()) {
            $model->log("Created followup service #{$request->id}");
            Yii::$app->session->addFlash("info", "New request created #{$request->id}");
        }


        return $this->redirect(['update', 'id' => $request->id]);
    }

    public function actionPrintReport($id)
    {
        $model = $this->findModel($id);
        $model->generatePdfReport();
        $path = Yii::getAlias("@staticWeb/upload/reports/{$model->random_token}.pdf?_=" . time());
        return $this->redirect($path);
    }

    public function actionTestReport($id)
    {
        $model = $this->findModel($id);
        return $model->generatePdfReport(false, false, true);
        $path = Yii::getAlias("@staticWeb/upload/reports/{$model->random_token}.pdf?_=" . time());
        return $this->redirect($path);
    }

    public function actionViewReport($id)
    {
        $model = $this->findModel($id);
        //$model->generatePdfReport();
        $path = Yii::getAlias("@staticWeb/upload/reports/{$model->random_token}.pdf?_=" . time());
        return $this->redirect($path);
    }

    public function actionViewTif($id)
    {
        $model = $this->findModel($id);

        $path = Yii::getAlias("@static/upload/reports");
        $url = Yii::getAlias("@staticWeb/upload/reports");

        $resolutions = [
            //            [400, 400, 5],
            //            [300, 300, 100],
            //            [300, 300, 50],
            //            [300, 300, 20],
            //            [300, 300, 5],
            //            [200, 200, 100],
            //            [200, 200, 50],
            //            [200, 200, 20],
            //            [200, 200, 5],
            //            [150, 150, 100],
            //            [150, 150, 50],
            //            [150, 150, 20],
            //            [100, 100, 300],
            //            [100, 100, 100],
            //            [100, 100, 50],
            //            [100, 100, 20],
            [140, 140, 80],
            [140, 140, 70],
            [140, 140, 60],
            [140, 140, 50],
            [140, 140, 40],
            [140, 140, 30],
        ];

        foreach ($resolutions as $resolution) {
            list($x, $y, $r) = $resolution;
            $tifPath = $path . DIRECTORY_SEPARATOR . "test-{$x}-{$y}-{$r}.tif";
            $im2 = new Imagick();
            $im2->setResolution($x, $y);
            $im2->setCompression(Imagick::COMPRESSION_JPEG);
            $im2->setCompressionQuality($r);
            $im2->readImage($path . DIRECTORY_SEPARATOR . $model->random_token . ".pdf");
            $im2->setImageCompression(true);
            $im2->setImageFormat("tiff");
            $im2->setImageColorSpace(Imagick::COLORSPACE_RGB);
            $im2->stripImage();
            $im2->writeImages($tifPath, true);
        }
        echo "Done";
        //return $this->redirect($url . DIRECTORY_SEPARATOR . $model->random_token . ".tif");
    }

    public function actionTasks($id)
    {
        $request = $this->findModel($id);
        $status = $request->status;

        if ($request->service_type == RepairRequest::TYPE_PPM || $request->division_id == Division::DIVISION_PLANT) {

            if ($request->division_id == Division::DIVISION_MALL) {

                return $this->render('tasks', [
                    'model' => $request,
                    'status' => $status,
                ]);
            } else if ($request->division_id == Division::DIVISION_PLANT) {

                $choose_tasks = true;

                return $this->render('plant-tasks', [
                    'model' => $request,
                    'choose_tasks' => $choose_tasks,
                    'status' => $status,
                ]);
            } else if ($request->division_id == Division::DIVISION_VILLA) {
                return $this->render('villa-tasks', [
                    'model' => $request,
                    'status' => $status,

                ]);
            }
        }
    }

    public function actionChangeTeam($id)
    {

        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $selection = Yii::$app->request->post('selection');
            $assignee_status = Yii::$app->request->post('Assignee_status');

            $out = [];

            if (!empty($selection)) {
                foreach ($assignee_status as $id => $status) {
                    if (in_array($id, $selection)) {
                        $out[] = ["id" => $id, "status" => $status];
                    }
                }
            } else {
                $model->checkMissingAssignees([], true);
            }

            if (!empty($out)) {
                if ($model->checkMissingAssignees($out, true)) {
                    Yii::$app->session->setFlash('success', "Team Changed Successfully");
                }
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }
    public function actionChangeStatus($id)
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $request = $this->findModel($id);
            $status = Yii::$app->request->post('status');

            if (!empty($status)) {
                if ($status == RepairRequest::STATUS_COMPLETED) {
                    $request->admin_signature = Yii::$app->user->identity->signature_url;
                }
                $request->status = $status;

                if ($request->save()) {

                    $statuses = (new RepairRequest())->status_list;
                    Log::AddLog(null, $request->id, Log::TYPE_REPAIR_REQUEST, "Work Order Status", "Work Status Changed To: " . $statuses[$status], $status);

                    return [
                        'response' => 'success'
                    ];
                } else {
                    print_r($request->errors);
                    exit;
                }
            }



        }
    }

    public function actionAddPlantPpmTasks($id)
    {
        $request = $this->findModel($id);
        $status = $request->status;
        if (Yii::$app->request->isPost) {

            $selection = Yii::$app->request->post('selection');

            if (!empty($selection)) {
                foreach ($selection as $task) {
                    $task = Json::decode($task);

                    // {"id":"3","name":"Water Separator Fuel Filter","task_type":"20","occurence_value":"500","meter_type":"20"
                    //,"status":"20","created_at":"2023-09-28 10:39:14","updated_at":"2023-09-28 10:39:14","created_by":"70","updated_by":"70"}

                    $plant_ppm_task_history = new PlantPpmTasksHistory();
                    $plant_ppm_task_history->task_id = $task['id'];
                    $plant_ppm_task_history->meter_ratio = 1;
                    $plant_ppm_task_history->asset_id = $request->equipment_id;
                    $plant_ppm_task_history->ppm_service_id = $request->id;
                    $plant_ppm_task_history->status = PlantPpmTasksHistory::STATUS_PENDING;
                    $plant_ppm_task_history->task_type = $task['task_type'];
                    if ($plant_ppm_task_history->save()) {
                        Yii::$app->session->setFlash('success', "Success!!");
                    }
                }

                return $this->redirect(['add-plant-ppm-tasks', 'id' => $id]);
            }
        }

        $choose_tasks = true;

        return $this->render('plant-tasks', [
            'model' => $request,
            'status' => $status,
            'choose_tasks' => $choose_tasks
        ]);
    }

    public function actionChats($id)
    {
        $request = $this->findModel($id);

        $chats = $request->repairRequestChats;

        return $this->render('chats', [
            'model' => $request,
            'chats' => $chats
        ]);
    }

    public function actionSendMessage($id)
    {
        $request = $this->findModel($id);
        $user_id = yii::$app->user->id;

        if (Yii::$app->request->isPost) {
            $message = Yii::$app->request->post('message');
            $image = Yii::$app->request->post('image');

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
                        ImageUploadHelper::uploadBase64Image(base64_encode($image), $imageModel);
                    }

                    $chats = $request->repairRequestChats;

                    return $this->render('chats', [
                        'model' => $request,
                        'chats' => $chats
                    ]);
                } else {
                    print_r($model->errors);
                    exit;
                }
            }
        }

        // return $this->redirect(['chats', 'id' => $id]);
    }

    public function actionSetLaborCharge($id)
    {
        $request = $this->findModel($id);
        $user_id = yii::$app->user->id;

        if (Yii::$app->request->isPost) {

            $labor_charge = Yii::$app->request->post('RepairRequest')['labor_charge'];

            if (!empty($labor_charge)) {
                $formatted_labor_charge = number_format((float) $labor_charge, 2, '.', '');
                $request->labor_charge = $formatted_labor_charge;
                if ($request->save()) {

                    $request->refresh();
                    Yii::$app->session->setFlash('success', 'Labor Charge Changed Successfully!!');
                    return $this->redirect(['view', 'id' => $request->id]);
                } else {
                    print_r($request->errors);
                    exit;
                }
            }
        }

        return $this->redirect(['view', 'id' => $request->id]);
    }
}
