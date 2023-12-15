<?php


namespace console\controllers;


use common\components\notification\Notification;
use common\components\settings\Setting;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Log;
use common\models\Maintenance;
use common\models\MallPpmTasksHistory;
use common\models\PlantPpmTasksHistory;
use common\models\RepairRequest;
use common\models\Sector;
use common\models\Technician;
use common\models\VehicleOilChangeHistory;
use common\models\VillaPpmTemplates;
use DateTime;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class CronController extends Controller
{

    public function actionClearNotifications()
    {
        $countToDelete = Notification::find()
            ->where([
                'AND',
                //['seen' => true],
                ["<=", 'created_at', date("Y-m-d H:i:s", strtotime("-2 days"))]
            ])->count();
        echo "Deleting {$countToDelete} Notifications";
        echo PHP_EOL;
        $countDeleted = Notification::deleteAll([
            'AND',
            //['seen' => true],
            ["<=", 'created_at', date("Y-m-d H:i:s", strtotime("-2 days"))]
        ]);
        echo "Deleted {$countDeleted} Notifications";
        echo PHP_EOL;
    }

    public function actionCheckMallPpmTasks()
    {
        // Loop On All Assets 
        $assets = LocationEquipments::find()->innerJoinWith('equipment')->innerJoinWith('location')->select([
            LocationEquipments::tableName() . '.id',
            LocationEquipments::tableName() . '.location_id',
            LocationEquipments::tableName() . '.value',
            Equipment::tableName() . '.equipment_type_id',
            Equipment::tableName() . '.category_id',
            Location::tableName() . '.sector_id',
        ])->where([LocationEquipments::tableName() . '.division_id' => Division::DIVISION_MALL])->asArray()->all();
        $count = 0;
        $year = (new DateTime())->format("Y");
        $hm = date('z') + 1;

        if (!empty($assets)) {
            $start_time = time();
            foreach ($assets as $asset) {
                $asset_id = $asset['id'];
                $ET = @$asset['equipment_type_id'];
                // $service_id = $this->id;
                $command = Yii::$app->db->createCommand('
                SELECT t.id, (FLOOR(:hm / t.occurence_value) + :year) as occurence, :asset as asset,  :year as year, :status as status
                FROM mall_ppm_tasks_history h
                RIGHT JOIN mall_ppm_tasks t ON h.task_id = t.id AND h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year)
                    AND h.asset_id = :asset AND h.year = :year
                WHERE FLOOR(:hm / t.occurence_value) > 0 AND h.id IS NULL AND t.equipment_type_id = :ET
            ', [
                    ':asset' => $asset_id,
                    ':hm' => $hm,
                    ':year' => $year,
                    ':status' => MallPpmTasksHistory::STATUS_PENDING,
                    ':ET' => $ET,
                ]);

                $result = $command->queryAll();

                if (count($result) > 0) {
                    $count++;

                    // Create a new repair req then assign it's id to the ppm history table
                    $repairOrder = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_CRONJOB]);
                    $repairOrder->division_id = Division::DIVISION_MALL;
                    $repairOrder->category_id = $asset['category_id'];
                    $repairOrder->sector_id = $asset['sector_id'];
                    $repairOrder->location_id = $asset['location_id'];
                    $repairOrder->equipment_id = $asset['id'];
                    $repairOrder->repair_request_path = @Equipment::getLayersValueTextInput($asset['value'], ",\n");
                    $repairOrder->service_type = RepairRequest::TYPE_PPM;
                    $repairOrder->status = RepairRequest::STATUS_DRAFT;
                    $repairOrder->need_review = true;
                    $repairOrder->urgent_status = false;
                    if ($repairOrder->save()) {
                        $repairOrder->refresh();
                        $repairOrder->createMallPpmTask();
                    }
                }
            }

            $end_time = time();

            print_r("Start Time: " . date("Y-m-d H:i:s", $start_time) . "\n"); // Format and print the start time
            print_r("End Time: " . date("Y-m-d H:i:s", $end_time) . "\n"); // Format and print the end time
            print_r("Count: " . $count . "\n");
            return true;
        }
    }

    public function actionCheckPlantPpmTasks()
    {
        // Loop On All Assets 
        $assets = LocationEquipments::find()->innerJoinWith('equipment')->innerJoinWith('location')->innerJoinWith('equipment.equipmentType')->select([
            LocationEquipments::tableName() . '.id',
            LocationEquipments::tableName() . '.location_id',
            LocationEquipments::tableName() . '.value',
            LocationEquipments::tableName() . '.meter_value',
            LocationEquipments::tableName() . '.meter_damaged',
            // Equipment::tableName() . '.equipment_type_id',
            Equipment::tableName() . '.category_id',
            EquipmentType::tableName() . '.meter_type',
            EquipmentType::tableName() . '.reference_value',
            EquipmentType::tableName() . '.equivalance',
            Location::tableName() . '.sector_id',

        ])->where([LocationEquipments::tableName() . '.division_id' => Division::DIVISION_PLANT])->asArray()->all();
        $count = 0;


        if (!empty($assets)) {
            $start_time = time();
            foreach ($assets as $asset) {
                $hm = $asset['meter_value'];
                $meter_type = $asset['meter_type'];
                $asset_id = $asset['id'];

                // DAMAGED
                $is_damged = $asset['meter_damaged'];

                $equivalance = $asset['equivalance'];
                $reference_value = $asset['reference_value'];

                if ($is_damged == 0) {
                    $last_repair_request = RepairRequest::find()->where([
                        'equipment_id' => $asset_id,
                        'service_type' => RepairRequest::TYPE_PPM,
                        // 'status' => RepairRequest::STATUS_COMPLETED,
                    ])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->one();

                    if (!empty($last_repair_request)) {
                        $created_at = strtotime($last_repair_request->created_at);
                        $today = strtotime(date('Y-m-d'));

                        $date_diff = floor(($today - $created_at) / (60 * 60 * 24)); // Difference in days

                        if ($date_diff >= $equivalance) {
                            // $asset_model = LocationEquipments::findOne($asset_id);

                            // $asset_model->meter_value = ($hm + $reference_value);

                            $new_hour_meter = $hm + $reference_value;

                            LocationEquipments::updateAll(['meter_value' => $new_hour_meter], ['id' => $asset_id]);
                            $hm = $new_hour_meter;
                        }
                    }
                }

                // $ET = @$asset['equipment_type_id'];
                // $service_id = $this->id;

                $command = null;
                $filter_command = null;

                if ($meter_type == EquipmentType::METER_TYPE_HOUR) {
                    $command = Yii::$app->db->createCommand('
                    #insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`)
                    select t.id,floor(:hm/t.occurence_value),:asset,10 from plant_ppm_tasks_history h right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = floor(:hm/t.occurence_value) and h.asset_id=:asset 
                    where floor(:hm/t.occurence_value)>0 and t.meter_type=:meter_type and h.id is null
                    ', [
                        ':asset' => $asset_id,
                        ':hm' => $hm,
                        ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING,
                        // ':ET' => $ET,
                        ':meter_type' => $meter_type
                    ]);
                } else if ($meter_type == EquipmentType::METER_TYPE_KM) {

                    $next_oil_change = VehicleOilChangeHistory::find()->where(['asset_id' => $asset_id])->orderBy(['datetime' => SORT_DESC])->one();

                    if (empty($next_oil_change)) {
                        $next_oil_change = 0;
                    } else {
                        $next_oil_change = $next_oil_change->next_oil_change;
                    }

                    if ($hm >= $next_oil_change) {
                        $command = Yii::$app->db->createCommand('
                        SELECT p.id,floor(:hm/:next_oil_change) as meter_ratio,:asset_id as asset,10 as status, p.task_type 
                        FROM plant_ppm_tasks p LEFT JOIN plant_ppm_tasks_history h ON h.task_id = p.id AND h.asset_id = :asset_id 
                        WHERE p.id = 1 AND h.asset_id NOT IN (
                        SELECT asset_id from oil_change_due WHERE next_oil_change = :next_oil_change  AND asset_id = :asset_id
                        )
                     ', [
                            ':asset_id' => $asset_id,
                            ':hm' => $hm,
                            ":next_oil_change" => $next_oil_change
                        ]);
                    }

                    $filter_command = Yii::$app->db->createCommand('
                    select t.id,floor(:hm/10000) as meter_ratio,:asset as asset_id,10 as status , t.task_type from plant_ppm_tasks_history h
                    right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = floor(:hm/10000) and h.asset_id=:asset 
                    where t.id = 2 AND floor(:hm/10000)> 0  and h.id is null
                    ', [
                        ':asset' => $asset_id,
                        ':hm' => $hm,
                        ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING,
                        // ':ET' => $ET,
                    ]);

                    $filter_result = !empty($filter_command) ? $filter_command->queryAll() : null;
                }

                $result = !empty($command) ? $command->queryAll() : null;

                if (!empty($result) || !empty($filter_result)) {

                    $count++;

                    // Create a new repair req then assign it's id to the ppm history table
                    $repairOrder = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_CRONJOB]);
                    $repairOrder->division_id = Division::DIVISION_PLANT;
                    $repairOrder->category_id = $asset['category_id'];
                    $repairOrder->sector_id = $asset['sector_id'];
                    $repairOrder->location_id = $asset['location_id'];
                    $repairOrder->equipment_id = $asset['id'];
                    $repairOrder->repair_request_path = @Equipment::getLayersValueTextInput($asset['value'], ",\n");
                    $repairOrder->service_type = RepairRequest::TYPE_PPM;
                    $repairOrder->status = RepairRequest::STATUS_DRAFT;
                    $repairOrder->need_review = true;
                    $repairOrder->urgent_status = false;
                    if ($repairOrder->save()) {
                        $repairOrder->refresh();
                        if ($meter_type == EquipmentType::METER_TYPE_HOUR) {
                            $repairOrder->createPlantPpmTask($repairOrder->id, $hm, $meter_type, $asset_id);
                        } else if ($meter_type == EquipmentType::METER_TYPE_KM) {
                            $repairOrder->createPlantPpmTaskKm($asset_id, $repairOrder->id, $next_oil_change, $result, $filter_result);
                        }
                    }
                }
            }

            $end_time = time();

            print_r("Start Time: " . date("Y-m-d H:i:s", $start_time) . "\n"); // Format and print the start time
            print_r("End Time: " . date("Y-m-d H:i:s", $end_time) . "\n"); // Format and print the end time
            print_r("Count: " . $count . "\n");
            return true;
        }
    }

    public function actionCheckPlantChecklistTasks()
    {
        // Loop On All Assets 
        $assets = LocationEquipments::find()->innerJoinWith('equipment')->innerJoinWith('location')->innerJoinWith('equipment.equipmentType')->select([
            LocationEquipments::tableName() . '.id',
            LocationEquipments::tableName() . '.location_id',
            LocationEquipments::tableName() . '.value',
            LocationEquipments::tableName() . '.meter_value',
            LocationEquipments::tableName() . '.meter_damaged',
            // Equipment::tableName() . '.equipment_type_id',
            Equipment::tableName() . '.category_id',
            EquipmentType::tableName() . '.meter_type',
            Location::tableName() . '.sector_id',

        ])->where([LocationEquipments::tableName() . '.division_id' => Division::DIVISION_PLANT])->asArray()->all();
        $count = 0;

        if (!empty($assets)) {
            $start_time = time();
            foreach ($assets as $asset) {
                $hm = date('z') + 1;
                $year = (new DateTime())->format("Y");
                $asset_id = $asset['id'];

                $command = Yii::$app->db->createCommand('
                    #insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`,`task_type`)
                    select t.id,(FLOOR(:hm / t.occurence_value) + :year),:asset_id,10 from plant_ppm_tasks_history h right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year) and h.asset_id= :asset_id 
                    where (FLOOR(:hm / t.occurence_value) + :year) > 0 and t.meter_type=30 and h.id is null
             ', [
                    ':asset_id' => $asset_id,
                    ':hm' => $hm,
                    ':year' => $year,
                ]);

                $result = !empty($command) ? $command->queryAll() : null;

                if (!empty($result)) {
                    $count++;

                    // Create a new repair req then assign it's id to the ppm history table
                    $repairOrder = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_CRONJOB]);
                    $repairOrder->division_id = Division::DIVISION_PLANT;
                    $repairOrder->category_id = $asset['category_id'];
                    $repairOrder->sector_id = $asset['sector_id'];
                    $repairOrder->location_id = $asset['location_id'];
                    $repairOrder->equipment_id = $asset['id'];
                    $repairOrder->repair_request_path = @Equipment::getLayersValueTextInput($asset['value'], ",\n");
                    $repairOrder->service_type = RepairRequest::TYPE_PPM;
                    $repairOrder->status = RepairRequest::STATUS_CREATED;
                    $repairOrder->need_review = false;
                    $repairOrder->urgent_status = false;
                    if ($repairOrder->save()) {
                        $repairOrder->refresh();
                        $repairOrder->createPlantPpmChecklistTask($repairOrder->id, $asset_id);
                    }
                }
            }

            $end_time = time();

            print_r("Start Time: " . date("Y-m-d H:i:s", $start_time) . "\n"); // Format and print the start time
            print_r("End Time: " . date("Y-m-d H:i:s", $end_time) . "\n"); // Format and print the end time
            print_r("Count: " . $count . "\n");
            return true;
        }
    }

    public function actionCheckVillaPpmTasks()
    {
        $templates = VillaPpmTemplates::findEnabled()->all();
        $can_create_request = false;

        if (!empty($templates)) {
            foreach ($templates as $template) {
                $can_create_request = false;
                $duration = $template->frequency;
                $starting_date = Yii::$app->formatter->asDate($template->starting_date_time);
                $repeating_cond = $template->repeating_condition;
                $request = RepairRequest::find()->where(['template_id' => $template->id]);

                if ($repeating_cond == VillaPpmTemplates::REPEATING_FIXED_DATE) {
                    $request = $request->orderBy(['created_at' => SORT_DESC])->one();

                    if (!empty($request)) {
                        $createdAtTimestamp = strtotime($request->created_at); // Assuming $request->created_at is a valid date string
                        $durationInSeconds = $duration; // Assuming $duration is in seconds

                        $newTimestamp = $createdAtTimestamp + $durationInSeconds;

                        $calculated_date = Yii::$app->formatter->asDate($newTimestamp);

                        if (Yii::$app->formatter->asDate(gmdate("Y-m-d")) >= $calculated_date) {
                            // Create new rpair request
                            $can_create_request = true;
                        }
                    } else {
                        if (Yii::$app->formatter->asDate(gmdate("Y-m-d")) >= $starting_date) {

                            // Create new rpair request
                            $can_create_request = true;

                        }
                    }
                } else if ($repeating_cond == VillaPpmTemplates::REPEATING_WHEN_COMPLETED) {
                    $request = $request->orderBy(['completed_at' => SORT_DESC])->one();

                    if (!empty($request)) {
                        $createdAtTimestamp = strtotime($request->completed_at); // Assuming $request->created_at is a valid date string
                        $durationInSeconds = $duration; // Assuming $duration is in seconds

                        $newTimestamp = $createdAtTimestamp + $durationInSeconds;

                        $calculated_date = Yii::$app->formatter->asDate($newTimestamp);

                        if (Yii::$app->formatter->asDate(gmdate("Y-m-d")) >= $calculated_date) {
                            // Create new rpair request
                            $can_create_request = true;
                        }
                    } else {
                        if (Yii::$app->formatter->asDate(gmdate("Y-m-d")) >= $starting_date) {
                            // Create new rpair request
                            $can_create_request = true;
                        }
                    }
                }

                if ($can_create_request) {
                    $repairOrder = new RepairRequest(['scenario' => RepairRequest::SCENARIO_CREATE_CRONJOB]);
                    $repairOrder->division_id = Division::DIVISION_VILLA;
                    $repairOrder->category_id = $template->category_id;
                    $repairOrder->sector_id = $template->sector_id;
                    $repairOrder->location_id = $template->location_id;
                    $repairOrder->equipment_id = $template->asset_id;
                    $repairOrder->repair_request_path = $template->path;
                    $repairOrder->service_type = RepairRequest::TYPE_PPM;
                    $repairOrder->status = RepairRequest::STATUS_CREATED;
                    $repairOrder->need_review = false;
                    $repairOrder->urgent_status = false;
                    $repairOrder->scheduled_at = gmdate("Y-m-d H:i:s");
                    $repairOrder->template_id = $template->id;
                    if ($repairOrder->save()) {
                        Log::AddLog(null, $repairOrder->id, Log::TYPE_REPAIR_REQUEST, "Work Order Creation", "Work Order #{$repairOrder->id} Was Created", $repairOrder->status);
                        $repairOrder->refresh();

                        if (!empty($template->team_members))
                            $repairOrder->checkMissingAssigneesWithoutStatus(explode(',', $template->team_members), true);

                        if (!empty($template->tasks))
                            $repairOrder->createVillaPpmTask($template->asset_id, $repairOrder->id, explode(',', $template->tasks));
                    }
                }
            }
        }
    }

    public function actionCheckVillaExpiryDates()
    {
        $villas = Location::find()->where(['division_id' => Division::DIVISION_VILLA, 'status' => Location::STATUS_ENABLED])->andWhere(['IS NOT', 'expiry_date', null])
            ->all();

        $count = 0;

        if (!empty($villas)) {
            foreach ($villas as $villa) {
                $expiryDate = strtotime($villa->expiry_date);
                $twoWeeksAhead = strtotime('+3 weeks');

                if ($expiryDate <= $twoWeeksAhead) {

                    $count++;

                    $send_to = Setting::getValue('contact_email');

                    Yii::$app->mailer
                        ->compose('notification', [
                            'data' => [
                                'title' => 'Villa Contract Expiry Warning',
                                'message' => "The Villa #{$villa->code} | {$villa->name} Is About To Expire In {$villa->expiry_date}",
                                'button_label' => 'Check Villa',
                                'button_link' => Yii::$app->urlManagerAdmin->createAbsoluteUrl(['location/view', 'id' => $villa->id])
                            ]
                        ])
                        ->setFrom(Yii::$app->params['noreplyEmail'])
                        ->setTo(explode(',', $send_to))
                        ->setSubject('Villa Contract Expiry Warning')
                        // ->attach($path, ['fileName' => "monthly_dashboard_" . Yii::$app->formatter->asDate($from) . ".pdf"])
                        ->send();
                }
            }
        }

        print_r("Send Mails For {$count} Villas Successfully!!");

        return true;
    }


}
