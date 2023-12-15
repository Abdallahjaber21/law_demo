<?php


namespace console\controllers;


use common\models\Equipment;
use common\models\RepairRequest;
use yii\console\Controller;

class ReminderController extends Controller
{
    public function actionContracts()
    {
        $equipments = Equipment::find()
            ->where([
                'AND',
                [">=", 'expire_at', date("Y-m-d 00:00:00", strtotime("+7 days"))],
                ["<=", 'expire_at', date("Y-m-d 23:59:59", strtotime("+7 days"))],
            ])
            ->all();
        foreach ($equipments as $index => $equipment) {
            echo "Notifying about {$equipment->id} \n";
//            Notification::notifyCustomerUsers($contract->customer_id,
//                "Contract #{$contract->id} will expire in 7 days",
//                "Contract about to expire",[],['/'],
//                ['action'=>'view-contract', 'id'=>$contract->id],[], Notification::TYPE_CONTRACT);
        }
    }

    public function actionMaintenance()
    {
        /* @var $services RepairRequest[] */
        $services = RepairRequest::find()
            ->where([
                'AND',
                ["type" => RepairRequest::TYPE_SCHEDULED],
                [">=", 'scheduled_at', date("Y-m-d 00:00:00", strtotime("+1 day"))],
                ["<=", 'scheduled_at', date("Y-m-d 23:59:59", strtotime("+1 day"))],
            ])
            ->all();
        foreach ($services as $index => $service) {
            echo "Notifying about {$service->id} \n";
//            Notification::notifyCustomerUsers($service->equipment->location->customer_id,
//                "Service #{$service->id} is scheduled for tomorrow",
//                "Service #{$service->id} reminder",[],['/'],
//                ['action'=>'view-service', 'id'=>$service->id],[], Notification::TYPE_SERVICE, $service->id);
        }
    }
}