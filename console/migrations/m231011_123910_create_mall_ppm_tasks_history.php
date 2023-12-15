<?php

use common\models\LocationEquipments;
use common\models\MallPpmTasks;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231011_123910_create_mall_ppm_tasks_history
 */
class m231011_123910_create_mall_ppm_tasks_history extends CreateTableMigration
{
    public function getTableName()
    {
        return "mall_ppm_tasks_history";
    }

    public function columns()
    {
        return [
            "task_id" => $this->foreignKey(MallPpmTasks::tableName(), 'id', $this->integer()),
            "meter_ratio" => $this->integer(),
            "asset_id" => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            "ppm_service_id" => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            "year" => $this->integer(),
            'completed_at' => $this->dateTime(),
            'completed_by' => $this->integer(),
        ];
    }
}
