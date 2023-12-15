<?php

use common\models\LocationEquipments;
use common\models\PlantPpmTasks;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m230928_132018_create_plant_ppm_tasks_history
 */
class m230928_132018_create_plant_ppm_tasks_history extends CreateTableMigration
{
    public function getTableName()
    {
        return "plant_ppm_tasks_history";
    }

    public function columns()
    {
        return [
            "task_id" => $this->foreignKey(PlantPpmTasks::tableName(), 'id', $this->integer()),
            "meter_ratio" => $this->integer(),
            "asset_id" => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            "ppm_service_id" => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
        ];
    }
}
