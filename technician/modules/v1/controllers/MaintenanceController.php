<?php


namespace technician\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\components\helpers\ImageUploadHelper;
use common\models\BarcodeScan;
use common\models\Equipment;
use common\models\EquipmentMaintenanceBarcode;
use common\models\Gallery;
use common\models\Image;
use common\models\Location;
use common\models\Maintenance;
use common\models\MaintenanceReport;
use common\models\MaintenanceVisit;
use common\models\RepairRequest;
use common\models\Technician;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class MaintenanceController extends ApiController
{

    private function canCheckin()
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();
        //
        return $user->getRepairRequests()
            ->where([
                'AND',
                [
                    'status' => [
                        RepairRequest::STATUS_CHECKED_IN,

                    ]
                ],
                [
                    'type' => [
                        RepairRequest::TYPE_REQUEST,
                        RepairRequest::TYPE_SCHEDULED
                    ]
                ],
            ])
            ->count() == 0;
    }

    public function actionOpenLocation($id)
    {
        $this->isGet();
        $canCheckinOverride = $this->canCheckin();


        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        /** @var Maintenance[] $maintenances */
        $maintenances = Maintenance::find()
            ->with(['equipment', 'location'])
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['location_id' => $id],
                //                ['year' => (int)gmdate("Y")],
                //                ['month' => (int)gmdate("m")],
                //                ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]

                [
                    'OR',
                    [
                        'AND',
                        ['year' => (int)gmdate("Y")],
                        ['month' => (int)gmdate("m")],
                        ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]
                    ],
                    [
                        'AND',
                        ['year' => (int)gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['month' => (int)gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]
                    ]
                ]
            ])
            ->orderBy([
                'status'       => SORT_DESC,
                'year'         => SORT_ASC,
                'month'        => SORT_ASC,
                'visit_number' => SORT_ASC
            ])
            ->all();

        $currentVisit = MaintenanceVisit::find()
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['status' => [MaintenanceVisit::STATUS_ENABLED]]
            ])
            ->one();

        $result = [];
        $startedId = null;
        $completedEquipments = [];

        $daysDurationBetweenFirstAndSecondVisits = [
            '1-10'  => 10,
            '11-17' => 7,
            '18-25' => 3,
            '26-31' => 0
        ];

        $previousMonth = (int)gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'));

        $firstVisits = [];
        $daysSinceFirstVisitCompleted = [];
        $daysToPassBeforeAllowSecondVisit = [];
        $isSecondVisitOfThisMonthAllowedToStart = [];

        $numOfVisists = [];
        foreach ($maintenances as $index => $maintenance) {
            if ($maintenance->visit_number == "A") {
                if ($maintenance->month != $previousMonth) {
                    $firstVisits[$maintenance->equipment_id] = true;
                }

                if ($maintenance->status == Maintenance::STATUS_COMPLETE && $maintenance->month != $previousMonth) {
                    $now = time();
                    $completionDate = strtotime($maintenance->completed_at);
                    $datediff = $now - $completionDate;
                    $daysSince = round($datediff / (60 * 60 * 24));
                    $daysSinceFirstVisitCompleted[$maintenance->equipment_id] = $daysSince;

                    //calculate $daysToPassBeforeAllowSecondVisit
                    $day = (int)date('d', $completionDate);
                    $daysSpan = 0;
                    if ($day < 10) {
                        $daysSpan = 2;
                    } else if ($day < 17) {
                        $daysSpan = 2;
                    } else if ($day < 25) {
                        $daysSpan = 2;
                    }
                    $daysToPassBeforeAllowSecondVisit[$maintenance->equipment_id] = $daysSpan;

                    if ($daysSince >= $daysSpan) {
                        $isSecondVisitOfThisMonthAllowedToStart[$maintenance->equipment_id] = true;
                    }
                }
            }
            if (empty($numOfVisists[$maintenance->equipment_id])) {
                $numOfVisists[$maintenance->equipment_id] = 0;
            }
            if ($maintenance->month != $previousMonth) {
                $numOfVisists[$maintenance->equipment_id] = $numOfVisists[$maintenance->equipment_id] + 1;
            }
        }

        foreach ($maintenances as $index => $maintenance) {
            if ($maintenance->visit_number == "B") {
                if (empty($firstVisits[$maintenance->equipment_id])) {
                    $firstVisit = Maintenance::find()
                        ->where([
                            'AND',
                            ['equipment_id' => $maintenance->equipment_id],
                            ['month' => $maintenance->month],
                            ['year' => $maintenance->year],
                            ['visit_number' => 'A']
                        ])->one();
                    if (!empty($firstVisit)) {
                        //$firstVisits[$firstVisit->equipment_id] = true;
                        if ($firstVisit->status == Maintenance::STATUS_COMPLETE && $firstVisit->month != $previousMonth) {
                            $completedEquipments[$maintenance->equipment_id] = $maintenance->equipment_id;

                            $now = time();
                            $completionDate = strtotime($firstVisit->completed_at);
                            $datediff = $now - $completionDate;
                            $daysSince = round($datediff / (60 * 60 * 24));
                            $daysSinceFirstVisitCompleted[$firstVisit->equipment_id] = $daysSince;

                            //calculate $daysToPassBeforeAllowSecondVisit
                            $day = (int)date('d', $completionDate);
                            $daysSpan = 0;
                            if ($day < 10) {
                                $daysSpan = 2;
                            } else if ($day < 17) {
                                $daysSpan = 2;
                            } else if ($day < 25) {
                                $daysSpan = 2;
                            }
                            $daysToPassBeforeAllowSecondVisit[$firstVisit->equipment_id] = $daysSpan;

                            if ($daysSince >= $daysSpan) {
                                $isSecondVisitOfThisMonthAllowedToStart[$firstVisit->equipment_id] = true;
                            }
                        }
                    }
                }
            }
        }

        $hasFromPreviousMonth = [];
        /** @var Maintenance[] $maintenancesFromPreviousMonthOtherTechs */
        $maintenancesFromPreviousMonthOtherTechs = Maintenance::find()
            ->with(['equipment', 'location'])
            ->where([
                'AND',
                ['!=', 'technician_id', $user->id],
                ['location_id' => $id],
                [
                    'AND',
                    ['year' => (int)gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                    ['month' => (int)gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                    ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START]]
                ]
            ])
            ->orderBy([
                'status'       => SORT_DESC,
                'year'         => SORT_ASC,
                'month'        => SORT_ASC,
                'visit_number' => SORT_ASC
            ])
            ->count();
        foreach ($maintenances as $index => $maintenance) {
            if ($maintenance->month == $previousMonth) {
                if (($maintenance->status != Maintenance::STATUS_COMPLETE && $maintenance->status != Maintenance::STATUS_NOT_COMPLETE)
                    || ($maintenance->status == Maintenance::STATUS_COMPLETE && !$maintenance->report_generated)
                ) {
                    $hasFromPreviousMonth[$maintenance->location_id] = true;
                }
            }
        }
        if ($maintenancesFromPreviousMonthOtherTechs > 0) {
            $hasFromPreviousMonth[$maintenance->location_id] = true;
        }


        foreach ($maintenances as $index => $maintenance) {
            if (empty($result[$maintenance->location_id])) {
                $result[$maintenance->location_id] = [
                    'location_id'          => $maintenance->location_id,
                    'location_name'        => $maintenance->location->name,
                    'location_latitude'    => $maintenance->location->latitude,
                    'location_longitude'   => $maintenance->location->longitude,
                    'equipments'           => [],
                    'completed_equipments' => [],
                    'status'               => 99,
                    'can_generate_report'  => false,
                    'is_active'            => false,
                    'is_restricted'        => (bool)$maintenance->location->is_restricted,
                    'can_checkin'          => false,
                    'has_started'          => true,
                ];
            }


            if (empty($currentVisit)) {
                $result[$maintenance->location_id]['can_checkin'] = true && $canCheckinOverride;
            }

            if ($maintenance->status == Maintenance::STATUS_COMPLETE) {
                if ($maintenance->month == $previousMonth) {
                    if ($maintenance->report_generated) {
                        continue;
                    }
                }
                $result[$maintenance->location_id]['can_generate_report'] = ($result[$maintenance->location_id]['can_generate_report'] || !$maintenance->report_generated) && $canCheckinOverride;
                $result[$maintenance->location_id]['completed_equipments'][$maintenance->id] = $maintenance->toArray();
                $result[$maintenance->location_id]['completed_equipments'][$maintenance->id]['can_start'] = false;
                $result[$maintenance->location_id]['completed_equipments'][$maintenance->id]['completed_at'] = date("d/m/Y", strtotime($maintenance->completed_at . ' UTC'));

                if ($maintenance->visit_number == "B") {
                    $result[$maintenance->location_id]['completed_equipments'][$maintenance->id]['description'] = "Second Visit";
                } else {
                    $result[$maintenance->location_id]['completed_equipments'][$maintenance->id]['description'] = $numOfVisists[$maintenance->equipment_id] > 1 ? "First Visit" : null;
                }

                if ($maintenance->month == $previousMonth) {
                    $result[$maintenance->location_id]['completed_equipments'][$maintenance->id]['description'] .= " - From last month";
                }
                if ($maintenance->month != $previousMonth) {
                    $completedEquipments[$maintenance->equipment_id] = $maintenance->equipment_id;
                }
            } else {
                if ($maintenance->visit_number == "B") {
                    if (!empty($firstVisits[$maintenance->equipment_id])) {
                        if (empty($completedEquipments[$maintenance->equipment_id])) {
                            if ($maintenance->month != $previousMonth) {
                                continue;
                            }
                        }
                    }
                }
                $result[$maintenance->location_id]['equipments'][$maintenance->id] = $maintenance->toArray();
                $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = true;
                $result[$maintenance->location_id]['equipments'][$maintenance->id]['is_restricted'] = (bool)$maintenance->location->is_restricted;
                if ($maintenance->visit_number == "B") {
                    $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] = "Second Visit";
                    if (!empty($completedEquipments[$maintenance->equipment_id]) || empty($firstVisits[$maintenance->equipment_id])) {
                        //1st visit is completed, check if the second is allowed to be started
                        if (!empty($isSecondVisitOfThisMonthAllowedToStart[$maintenance->equipment_id])) {
                            $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = true;
                        } else {
                            $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = false;
                        }
                    }
                } else {
                    if ($maintenance->equipment->manufacturer == Equipment::MANUFACTURER_THIRD_PARTY) {
                        $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] = "First Visit";
                    }
                    //$result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] = $numOfVisists[$maintenance->equipment_id] > 1 ? "First Visit" : null;
                    $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = true;
                }
                if ($maintenance->month == $previousMonth) {
                    if (empty($result[$maintenance->location_id]['equipments'][$maintenance->id]['description'])) {
                        $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] = " - From last month";
                    } else {
                        $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] .= " - From last month";
                    }
                } else {
                    if ($maintenance->status == Maintenance::STATUS_START) {
                        if (empty($result[$maintenance->location_id]['equipments'][$maintenance->id]['description'])) {
                            $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] = " - " . date("M Y");
                        } else {
                            $result[$maintenance->location_id]['equipments'][$maintenance->id]['description'] .= " - " . date("M Y");
                        }
                    }
                }

                if (!empty($hasFromPreviousMonth[$maintenance->location_id])) {
                    if ($maintenance->month == $previousMonth) {
                        $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = true;
                    } else {
                        $result[$maintenance->location_id]['equipments'][$maintenance->id]['can_start'] = false;
                    }
                }
            }

            if ($maintenance->status == Maintenance::STATUS_START) {
                $startedId = $maintenance->id;
            }

            if (!empty($currentVisit) && $currentVisit->location_id == $maintenance->location_id) {
                $result[$maintenance->location_id]['is_active'] = true;
            }


            $result[$maintenance->location_id]['status'] = min($result[$maintenance->location_id]['status'], $maintenance->status);


            if ($result[$maintenance->location_id]['status'] == Maintenance::STATUS_COMPLETE) {
                $result[$maintenance->location_id]['can_checkin'] = false;
            }

            $result[$maintenance->location_id]['status_label'] = (new Maintenance(['status' => $result[$maintenance->location_id]['status']]))->status_label;
            $result[$maintenance->location_id]['description'] = \Yii::t('app', '{n,plural,=0{No equipments} =1{One equipment} other{# equipments}}', ['n' => count($result[$maintenance->location_id]['equipments'])]);

            $result[$maintenance->location_id]['extra_message']  = '';
            if ($maintenancesFromPreviousMonthOtherTechs > 0) {
                $result[$maintenance->location_id]['extra_message'] = "Some visits assigned to other technicians last month have not yet been completed. You will be able to proceed with your current maintenance visit once the past pending ones are completed.";
            }
        }
        if (!empty($startedId)) {
            foreach ($result[$maintenance->location_id]['equipments'] as $index => $equipment) {
                //$result[$maintenance->location_id]['equipments'][$index]['can_start'] = ($startedId == $index);
                //uncomment to only allow active one
            }
        }
        return isset($result[$id]) ? $result[$id] : ['equipments' => []];
    }

    public function actionCheckin()
    {
        $this->isPost();

        $id = \Yii::$app->request->post("id");

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $currentVisit = MaintenanceVisit::find()
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['status' => [MaintenanceVisit::STATUS_ENABLED]]
            ])
            ->one();

        if (!empty($currentVisit)) {
            throw new ServerErrorHttpException("Already checked in to {$currentVisit->location->name}");
        }

        /** @var Maintenance $maintenance */
        $maintenance = Maintenance::find()
            ->with(['equipment', 'location'])
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['location_id' => $id],
                //                ['year' => (int)gmdate("Y")],
                //                ['month' => (int)gmdate("m")],
                //                ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]
                [
                    'OR',
                    [
                        'AND',
                        ['year' => (int)gmdate("Y")],
                        ['month' => (int)gmdate("m")],
                        ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]
                    ],
                    [
                        'AND',
                        ['year' => (int)gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['month' => (int)gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]]
                    ]
                ]
            ])
            ->all();

        if (empty($maintenance)) {
            throw new ServerErrorHttpException("No maintenance tasks found for location");
        }

        $maintenanceVisit = new MaintenanceVisit();
        $maintenanceVisit->technician_id = $user->id;
        $maintenanceVisit->location_id = $id;
        $maintenanceVisit->status = MaintenanceVisit::STATUS_ENABLED;
        $maintenanceVisit->checked_in = gmdate("Y-m-d H:i:s");
        $maintenanceVisit->save();

        return ['success' => true];
    }

    public function actionCheckout()
    {
        $this->isPost();

        $id = \Yii::$app->request->post("id");

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $currentVisit = MaintenanceVisit::find()
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['location_id' => $id],
                ['status' => [MaintenanceVisit::STATUS_ENABLED]]
            ])
            ->one();
        if (!empty($currentVisit)) {
            $currentVisit->status = MaintenanceVisit::STATUS_COMPLETED;
            $currentVisit->checked_out = gmdate("Y-m-d H:i:s");
            $currentVisit->save(false);

            //            $maintenance = Maintenance::find()
            //                ->where([
            //                    'AND',
            //                    //['id' => $id],
            //                    ['technician_id' => $user->id],
            //                    ['status' => [Maintenance::STATUS_START]]
            //                ])
            //                ->one();
            //            if (!empty($maintenance)) {
            //                $maintenance->log("Put job on hold");
            //            }

            $scans = BarcodeScan::find()->with(['maintenance'])->where(['visit_id' => $currentVisit->id])->all();
            $maintenances = [];
            foreach ($scans as $index => $scan) {
                $maintenances[$scan->maintenance_id] = $scan->maintenance;
            }
            foreach ($maintenances as $index => $maintenance) {
                $maintenance->log("Checked out");
            }
        }

        return ['success' => true];
    }

    public function actionGetBarcodes($id)
    {
        $this->isGet();

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
                //['status' => [Maintenance::STATUS_PENDING]]
            ])
            ->one();
        if (empty($maintenance)) {
            throw new ServerErrorHttpException("Maintenance task not found");
        }
        $scanned = ArrayHelper::getColumn(BarcodeScan::find()
            ->select(['barcode_id'])
            ->where([
                'AND',
                ['maintenance_id' => $maintenance->id]
            ])
            ->asArray()
            ->all(), function ($model) {
            return (int)$model['barcode_id'];
        }, false);

        return [
            'is_restricted' => $maintenance->equipment->location->is_restricted,
            'barcodes'      => $maintenance->equipment->equipmentMaintenanceBarcodes,
            'scanned'       => $scanned,
            'client_note'   => $maintenance->note,
            'internal_note' => $maintenance->internal_notes,
        ];
    }

    public function actionReject()
    {
        $this->isPost();
        $id = Yii::$app->request->post("id");
        $eqid = Yii::$app->request->post("eq_id");
        $reason = Yii::$app->request->post("reason");
        if (empty($reason)) {
            throw new ServerErrorHttpException("Reason cannot be empty");
        }

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenances = Maintenance::find()
            ->where([
                'AND',
                ['equipment_id' => $eqid],
                ['technician_id' => $user->id],
                ['status' => [Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START]],
            ])
            ->all();

        if (empty($maintenances)) {
            throw new ServerErrorHttpException("Maintenances job not found");
        }

        foreach ($maintenances as $index => $maintenance) {
            $maintenance->status = Maintenance::STATUS_PENDING;
            $maintenance->technician_id = null;
            $maintenance->is_previously_rejected = true;
            $maintenance->log("Rejected the job, reason:" . $reason);
            $maintenance->save(false);
        }

        return ['success' => true];
    }

    public function actionRejectAll()
    {
        $this->isPost();
        $id = Yii::$app->request->post("id");
        $reason = Yii::$app->request->post("reason");
        if (empty($reason)) {
            throw new ServerErrorHttpException("Reason cannot be empty");
        }

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenances = Maintenance::find()
            ->where([
                'AND',
                ['location_id' => $id],
                ['technician_id' => $user->id],
                ['status' => [Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START]],
            ])
            ->all();

        if (empty($maintenances)) {
            throw new ServerErrorHttpException("Maintenances job not found");
        }

        foreach ($maintenances as $index => $maintenance) {
            $maintenance->status = Maintenance::STATUS_PENDING;
            $maintenance->technician_id = null;
            $maintenance->log("Rejected the job, reason:" . $reason);
            $maintenance->save(false);
        }

        return ['success' => true];
    }

    public function actionPutOnHold()
    {
        $this->isPost();
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();
        $id = Yii::$app->request->post("id");
        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
            ])
            ->one();

        if ($maintenance->status == Maintenance::STATUS_ASSIGNED) {
            $maintenance->status = Maintenance::STATUS_START;
            $maintenance->save(false);
            $maintenance->log("Put the job on Hold");
        }
    }

    public function actionForceFinish()
    {
        $this->isPost();
        $id = Yii::$app->request->post("id");
        $datetime = Yii::$app->request->post("datetime");
        $noError = Yii::$app->request->post("noError");
        if (empty($datetime)) {
            $datetime = gmdate("Y-m-d H:i:s");
        } else {
            $datetime = gmdate("Y-m-d H:i:s", strtotime($datetime));
            $datetime = str_replace("60", "00", $datetime);
        }
        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
                //['status' => [Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE]],
            ])
            ->one();

        if (empty($maintenance)) {
            if ($noError) {
                return ['success' => false];
            } else {
                throw new ServerErrorHttpException("Maintenance job not found");
            }
        }

        if ($maintenance->equipment->location->is_restricted) {
            $hard_copy_report = Yii::$app->getRequest()->post("hard_copy_report");
            if (empty($hard_copy_report)) {
                throw new ServerErrorHttpException("Please attache an image of the hard copy report");
            }
            ImageUploadHelper::uploadBase64Image($hard_copy_report, $maintenance, "hard_copy_report");
            $maintenance->status = Maintenance::STATUS_START;
            $maintenance->save(false);
        }


        if (in_array($maintenance->status, [Maintenance::STATUS_START, Maintenance::STATUS_COMPLETE])) {
            $maintenance->completed_at = $datetime; //gmdate("Y-m-d H:i:s");
            if ($maintenance->status == Maintenance::STATUS_START) {
                $maintenance->status = Maintenance::STATUS_COMPLETE;
                if ($maintenance->remaining_barcodes == 0) {
                    $maintenance->complete_method = Maintenance::COMPLETE_SCAN_ALL;
                } else {
                    $maintenance->complete_method = Maintenance::COMPLETE_PARTIAL;
                }
                $maintenance->log("Force Completed the job", null, $datetime);
            }
            $maintenance->atl_status = Maintenance::STATUS_PENDING;
            $maintenance->save(false);
        }

        return ['success' => true];
    }

    public function actionScanBarcode($id, $task_id = null, $barcode = null, $noError = false, $datetime = null)
    {
        $this->isGet();
        $isLastOne = false;
        if (empty($datetime)) {
            $datetime = gmdate("Y-m-d H:i:s");
        } else {
            $datetime = gmdate("Y-m-d H:i:s", strtotime($datetime));
        }

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
                ['status' => [Maintenance::STATUS_PENDING, Maintenance::STATUS_ASSIGNED, Maintenance::STATUS_START]]
            ])
            ->one();

        if (empty($maintenance)) {
            if ($noError) {
                return ['success' => false];
            } else {
                throw new ServerErrorHttpException("Maintenance job not found");
            }
        }

        $scannedCode = EquipmentMaintenanceBarcode::find()
            ->where([
                'AND',
                ['equipment_id' => $maintenance->equipment_id],
                [
                    'OR',
                    ['id' => $task_id],
                    ['barcode' => $barcode],
                ]
            ])
            ->one();

        if (empty($scannedCode)) {
            if ($noError) {
                return ['success' => false];
            } else {
                throw new ServerErrorHttpException("Scanned code not found");
            }
        }

        $currentVisit = MaintenanceVisit::find()
            ->where([
                'AND',
                ['technician_id' => $user->id],
                ['status' => [MaintenanceVisit::STATUS_ENABLED]]
            ])
            ->one();

        if (
            BarcodeScan::find()
            ->where([
                'AND',
                ['barcode_id' => $scannedCode->id],
                ['maintenance_id' => $maintenance->id]
            ])
            ->count() == 0
        ) {
            $barcodeScan = new BarcodeScan();
            $barcodeScan->maintenance_id = $maintenance->id;
            $barcodeScan->barcode_id = $scannedCode->id;
            $barcodeScan->visit_id = $currentVisit->id;
            $barcodeScan->created_at = $datetime;
            $barcodeScan->updated_at = $datetime;

            if ($barcodeScan->save()) {

                $totalScannedBarcodes = BarcodeScan::find()
                    ->where([
                        'AND',
                        ['maintenance_id' => $maintenance->id]
                    ])
                    ->count();
                $totalBarcodes = $maintenance->equipment->getEquipmentMaintenanceBarcodes()->count();
                $maintenance->remaining_barcodes = $totalBarcodes - $totalScannedBarcodes;
                $maintenance->number_of_barcodes = $totalBarcodes;

                $maintenance->log("Scanned barcode {$barcode}", null, $datetime);
                if ($totalScannedBarcodes == 1 || empty($maintenance->first_scan_at)) {
                    $maintenance->first_scan_at = $datetime;
                    $maintenance->status = Maintenance::STATUS_START;
                    $maintenance->log("Started the maintenance (First scan)", null, $datetime);
                }
                if ($totalScannedBarcodes >= $totalBarcodes) {
                    //                    $maintenance->completed_at = $datetime;//gmdate("Y-m-d H:i:s");
                    //                    $maintenance->status = Maintenance::STATUS_COMPLETE;
                    //                    $maintenance->complete_method = Maintenance::COMPLETE_SCAN_ALL;
                    //                    $maintenance->atl_status = Maintenance::STATUS_PENDING;
                    //                    $maintenance->save(false);
                    $isLastOne = true;
                    $maintenance->log("Finished the maintenance (Scan All)", null, $datetime);
                } else {
                    $maintenance->status = Maintenance::STATUS_START;
                }
                $maintenance->save(false);
            }
        } else {
            if ($noError) {
                return ['success' => false];
            } else {
                throw new ServerErrorHttpException("Barcode already scanned");
            }
        }

        return [
            'success'   => true,
            'isLastOne' => $isLastOne,
        ];
    }


    public function actionGenerateReport()
    {
        $this->isPost();

        $location_id = Yii::$app->request->post('location_id');
        //        $notes = Yii::$app->request->post('notes');
        //        $internalNotes = Yii::$app->request->post('internalNotes');
        $preview = Yii::$app->request->post('preview');
        $maintenance_id = Yii::$app->request->post('maintenance_id');

        /* @var $user Technician */
        $user = \Yii::$app->getUser()->getIdentity();

        $maintenances = Maintenance::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['technician_id' => $user->id],
                //                ['year' => (int)gmdate("Y")],
                //                ['month' => (int)gmdate("m")],
                //                ['status' => [Maintenance::STATUS_COMPLETE]],
                ['report_generated' => false],
                [
                    'OR',
                    [
                        'AND',
                        ['year' => (int)gmdate("Y")],
                        ['month' => (int)gmdate("m")],
                        ['status' => [Maintenance::STATUS_COMPLETE]],
                    ],
                    [
                        'AND',
                        ['year' => (int)gmdate("Y", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['month' => (int)gmdate("m", strtotime(gmdate("Y-m-1") . ' -2 day'))],
                        ['status' => [Maintenance::STATUS_COMPLETE]],
                    ]
                ]
            ])
            ->all();

        if (!empty($maintenance_id)) {
            $maintenances = Maintenance::find()
                ->where([
                    'AND',
                    ['id' => $maintenance_id],
                    ['technician_id' => $user->id],
                    ['status' => [Maintenance::STATUS_COMPLETE]],
                    ['report_generated' => false]
                ])
                ->all();
        }

        if (empty($maintenances)) {
            throw new ServerErrorHttpException("No completed maintenance in selected location");
        }

        $maintenanceByType = [];
        $month = (int)gmdate("m");
        $year = (int)gmdate("Y");
        $customerName = null;
        $customerSignature = null;
        $technicianSignature = null;
        foreach ($maintenances as $index => $maintenance) {
            if (empty($location_id)) {
                $location_id = $maintenance->equipment->location_id;
            }
            //            $maintenance->note = $notes;
            //            $maintenance->internal_notes = $internalNotes;
            if (!$preview && !$maintenance->equipment->location->is_restricted) {
                $customer_name = Yii::$app->getRequest()->post("customer_name");
                $maintenance->customer_name = $customer_name;
                $customer_signature = Yii::$app->getRequest()->post("customer_signature");
                if (!empty($customer_signature)) {
                    ImageUploadHelper::uploadBase64Image($customer_signature, $maintenance, "customer_signature");
                }
                $technician_signature = Yii::$app->getRequest()->post("technician_signature");
                if (!empty($technician_signature)) {
                    ImageUploadHelper::uploadBase64Image($technician_signature, $maintenance, "technician_signature");
                }
                $maintenance->save(false);
                $customerName = $maintenance->customer_name;
                $customerSignature = $maintenance->customer_signature_path;
                $technicianSignature = $maintenance->technician_signature_path;
            }


            $mCode = $maintenance->equipment->getMaintenanceFormCode();
            if (!empty($mCode)) {
                if (empty($maintenanceByType[$mCode])) {
                    $maintenanceByType[$mCode] = [];
                }
                $maintenanceByType[$mCode][] = $maintenance;
            }
        }

        $reports = [];
        foreach ($maintenances as $index => $maintenance) {

            $month = $maintenance->month;
            $year = $maintenance->year;

            $mCode = $maintenance->equipment->getMaintenanceFormCode();
            $lookingForColumns = [];
            //            foreach ($maintenances as $index => $maintenance) {
            if (empty($lookingForColumns["{$month}{$maintenance->visit_number}"])) {
                $lookingForColumns["{$month}{$maintenance->visit_number}"] = [];
            }
            $lookingForColumns["{$month}{$maintenance->visit_number}"][] = "{$maintenance->equipment->code}:{$maintenance->visit_number}";
            //            }
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(Yii::getAlias("@static/maintenance/{$mCode}.xlsx"));
            $worksheet = $spreadsheet->getActiveSheet();
            $iterator = $worksheet->getRowIterator();
            $header = $iterator->current();
            $cellIterator = $header->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $columnCodes = [];
            foreach ($cellIterator as $cell) {
                /* @var $cell Cell */
                if (array_key_exists($cell->getValue(), $lookingForColumns)) {
                    $columnCodes[$cell->getColumn()] = $lookingForColumns[$cell->getValue()];
                }
            }

            $results = [];
            $currentCategory = "";
            $currentSubCategory = "";
            $i = 0;
            for ($iterator->next(); $iterator->valid(); $iterator->next()) {
                $i++;
                try {
                    $row = $iterator->current();
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    foreach ($cellIterator as $cell) {
                        if ($cell->getColumn() == "A") {
                            $value = $cell->getValue();
                            if (!empty($value)) {
                                $currentCategory = "{$i}:" . $cell->getValue();
                                if (empty($results[$currentCategory])) {
                                    $results[$currentCategory] = [];
                                }
                            }
                        } else if ($cell->getColumn() == "B") {
                            if (!empty($cell->getValue())) {
                                $currentSubCategory = "{$i}:" . $cell->getValue();
                                if (empty($results[$currentCategory][$currentSubCategory])) {
                                    $results[$currentCategory][$currentSubCategory] = [];
                                }
                            }
                        } else {
                            if (array_key_exists($cell->getColumn(), $columnCodes)) {
                                foreach ($columnCodes[$cell->getColumn()] as $equipmentCode) {
                                    $results[$currentCategory][$currentSubCategory][$equipmentCode] = $cell->getValue();
                                }
                            }
                        }
                    }
                } catch (Exception $exception) {
                    continue;
                }
            }
            $reports[$maintenance->id] = [$mCode, $results, $maintenance];
        }

        $location = Location::find()
            ->where(['id' => $location_id])
            ->one();
        $technicianName = $user->name;
        if (!$preview) {
            foreach ($reports as $maintenance_id => $report) {
                $reportId = $this->generatePdfReport($report[2], $report[0], $report[1], $location, $report[2]->month, $report[2]->year, $customerName, $customerSignature, $technicianName, $technicianSignature);
                //                $mms = $maintenanceByType[$mCode];
                //                foreach ($mms as $index => $maintenance) {
                $maintenance = $report[2];
                $maintenance->report_id = $reportId;
                $maintenance->report_generated = true;
                $maintenance->save(false);
                $maintenance->log("Generated report");
                //                }
            }
            return ['success' => true];
        } else {
            //TODO preview
            $urls = [];
            foreach ($reports as $maintenance_id => $report) {
                /* @var $maintenance Maintenance */
                $maintenance = $report[2];

                $key = "{$maintenance_id}<br/>({$maintenance->equipment->name} - {$maintenance->equipment->code})";
                $urls[$key] = $this->generatePdfReport($report[2], $report[0], $report[1], $location, $report[2]->month, $report[2]->year, $customerName, $customerSignature, $technicianName, $technicianSignature, $preview);
            }
            return $urls;
        }
        //        foreach ($maintenances as $index => $maintenance) {
        //            $maintenance->report_generated = true;
        //            $maintenance->save(false);
        //        }

        //        return [
        //            $mCode, $report, $location, gmdate("m"), gmdate("Y"), $customerName, $customerSignature, $technicianName, $technicianSignature, $notes
        //        ];
    }

    /**
     * @param $maintenance Maintenance
     * @param $form
     * @param $data
     * @param $location Location
     * @param $month
     * @param $year
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public function generatePdfReport($maintenance, $form, $data, $location, $month, $year, $customerName, $customerSignature, $technicianName, $technicianSignature, $preview = false)
    {
        $maintenanceReport = null;
        //        MaintenanceReport::find()
        //            ->where([
        //                'AND',
        //                ['location_id' => $location->id],
        //                ['year' => (int)$year],
        //                ['month' => (int)$month],
        //                ['report' => $form],
        //            ])
        //            ->one();
        if (empty($maintenanceReport)) {
            $maintenanceReport = new MaintenanceReport([
                'location_id'   => $location->id,
                'technician_id' => Yii::$app->user->id,
                'year'          => (int)$year,
                'month'         => (int)$month,
                'report'        => $form,
            ]);
            if (!$preview) {
                $maintenanceReport->save();
            } else {
                $maintenanceReport->random_token = "PREVIEW_" . Yii::$app->security->generateRandomString();
            }
        }
        $path = $preview ?
            Yii::getAlias("@static/upload/maintenance_reports/preview/{$maintenanceReport->year}/{$maintenanceReport->month}") :
            Yii::getAlias("@static/upload/maintenance_reports/{$maintenanceReport->year}/{$maintenanceReport->month}");
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf = new Pdf([
            'mode'        => Pdf::MODE_BLANK,
            'format'      => Pdf::FORMAT_A4, //[148, 210],//A5 => 148mm x 210mm
            'content'     => Yii::$app->controller->renderPartial("@admin/views/_maintenance_report", [
                'maintenance_id'      => $maintenance->id,
                'form'                => $form,
                'data'                => $data,
                'location'            => $location,
                'customerName'        => $customerName,
                'customerSignature'   => $customerSignature,
                'technicianName'      => $technicianName,
                'technicianSignature' => $technicianSignature,

                'year'  => $year,
                'month' => $month,
                'notes' => $maintenance->note,

                'maintenance' => $maintenance,
            ]),
            'options'     => [
                'title'        => \Yii::t("app", "Maintenance Report {id}-{location}-{form}/{year}-{month}", [
                    'location' => $location->code,
                    'year'     => $year,
                    'month'    => $month,
                    'form'     => $form,
                    'id'       => $maintenance->id,
                ]),
                'fontDir'      => array_merge($fontDirs, [
                    Yii::getAlias("@static/fonts"),
                ]),
                'fontdata'     => [
                    'fontellocheck' => [
                        'R' => 'fontello-check.ttf',
                    ],
                    'tajawal'       => [
                        'R'      => 'almarai-v5-arabic-regular.ttf',
                        'B'      => 'almarai-v5-arabic-regular.ttf',
                        'useOTL' => 0xFF, 'useKashida' => 75,
                    ],
                ],
                'default_font' => 'helvetica'
            ],
            'methods'     => [
                //                'SetFooter' => ['|{PAGENO}/{nb}|'],
            ],
            'cssInline'   => "
                    .ar{font-family: tajawal;}
                    .check{font-family: fontellocheck;}
                    .blue{color: rgb(55,150,255);}
                    .red{color: #ff3737;}
                    .small{font-size: 2.9mm;}
                    .medium{font-size: 3.0mm;}
                    .large{font-size: 3.8mm;}
                    .bold{font-weight: bold;}
                    .right{text-align: right;}
                    .center{text-align: center;}
                    table.bordered{padding: 7px;border: 1px dotted #ccc;}
                    table.from-to-table td{font-size: 9px;}
                    .border-top{border-top: 1px dotted #ccc;}
                    .border-right{border-right: 1px dotted #ccc;}
                    .border-left{border-left: 1px dotted #ccc;}
                    .border-bottom{border-bottom: 1px dotted #ccc;}
                    .border-bottom-dotted{border-bottom: 2px dotted #888;}
                    .border-top-dotted{border-top: 2px dotted #ccc;}
                    .border-all{border: 1px dotted #ccc;border-left: none;}
                    .padding-left{padding-left: 5px}
                    .all-border-bottom tr td{border-bottom: 2px dotted #ccc; padding-top:1mm}
                    .order-details{font-size: 8px;}
                    .order-details tr.title{font-size: 8px;}
                    .order-details tr.title{background-color: #fafafa;}
                    .order-details tr.title th{padding-top: 2px;border-bottom: 1px dotted #ccc;border-top: 1px dotted #ccc;}
                    .order-details tbody tr td{border-bottom: 1px dotted #ccc;}
                    .summary-1{margin-bottom:10px;margin-top:10px;font-size:9pt;padding-top:10px}
                    .summary-table .border-top{border-top: 1px dotted #ccc;}
                    .summary-table .border-bottom{border-bottom: 1px dotted #555;}
                ",
            'filename'    => $preview ? $maintenanceReport->getPreviewFilePath() : $maintenanceReport->getFilePath(),
            'destination' => 'F',
            //            'destination' => 'I',
        ]);
        $pdf->marginHeader = 0;
        $pdf->marginLeft = 7;
        $pdf->marginRight = 7;
        $pdf->marginTop = 7;
        $pdf->marginBottom = 7;

        $pdf->defaultFont = 'helvetica';
        $pdf->defaultFontSize = 4;

        $pdf->getApi()->AddFontDirectory(Yii::getAlias("@static/fonts"));

        $pdf->render();

        if (!$preview) {
            //Save client copy
            $path2 = Yii::getAlias("@static/upload/maintenance_reports/client/{$maintenanceReport->year}/{$maintenanceReport->month}");
            if (!file_exists($path2)) {
                if (!mkdir($path2, 0755, true) && !is_dir($path2)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $path2));
                }
            }
            $copyFile = $maintenanceReport->getClientFilePath();
            $originalFile = $maintenanceReport->getFilePath();
            copy($originalFile, $copyFile);


            //start save a tif version
            $tifPath = Yii::getAlias("@static/upload/maintenance_reports/{$maintenanceReport->year}/{$maintenanceReport->month}/{$maintenanceReport->id}_{$maintenanceReport->random_token}.tif");
            $im2 = new \Imagick();
            $im2->setResolution(100, 100);
            $im2->setCompressionQuality(50);
            $im2->setCompression(\Imagick::COMPRESSION_JPEG);
            $im2->readImage($maintenanceReport->getFilePath());
            $im2->setImageFormat("tiff");
            $im2->setImageColorSpace(\Imagick::COLORSPACE_RGB);
            $im2->writeImages($tifPath, true);
            //end save a tif version

            return $maintenanceReport->id;
        } else {
            return $maintenanceReport->getPreviewFileUrl();
        }
        return $maintenanceReport->getFilePath();
    }

    public function actionSaveNote()
    {
        $this->isPost();
        $user = Yii::$app->user->identity;
        $key = Yii::$app->request->post("key");
        $note = Yii::$app->request->post("note");
        $id = Yii::$app->request->post("id");
        $noError = Yii::$app->request->post("noError");
        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
            ])
            ->one();

        if (empty($maintenance)) {
            if ($noError) {
                return ['success' => false];
            } else {
                throw new ServerErrorHttpException("Maintenance not found");
            }
        }
        if ($key == "client_note") {
            $maintenance->note = $note;
        }
        if ($key == "internal_note") {
            $maintenance->internal_notes = $note;
        }
        $maintenance->save();
        return ['success' => true];
    }

    public function actionAttachImages()
    {
        $this->isPost();
        $user = Yii::$app->user->identity;
        $id = Yii::$app->request->post("id");
        $maintenance = Maintenance::find()
            ->where([
                'AND',
                ['id' => $id],
                ['technician_id' => $user->id],
            ])
            ->one();

        $images = \Yii::$app->getRequest()->post("images");

        if (!empty($images)) {
            $gallery = $maintenance->gallery;
            if (empty($gallery)) {
                $gallery = new Gallery();
                if ($gallery->save()) {
                    $maintenance->gallery_id = $gallery->id;
                    $maintenance->save(false);
                }
            }
            foreach ($images as $index => $image) {
                $imageModel = new Image();
                $imageModel->gallery_id = $gallery->id;
                $imageModel->save();
                ImageUploadHelper::uploadBase64Image($image, $imageModel);
            }
        }
        return ['success' => true];
    }
}
