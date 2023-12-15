<?php

use common\models\LocationEquipments;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231018_084808_create_table_ppm_additional_tasks_values
 */
class m231018_084808_create_table_ppm_additional_tasks_values extends CreateTableMigration
{
    public function getTableName()
    {
        return "ppm_additional_tasks_values";
    }

    public function columns()
    {
        return [
            "ppm_service_id" => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            "asset_id" => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            "additional_task_id" => $this->foreignKey('ppm_additional_tasks', 'id', $this->integer()),
            "value" => $this->string(),
        ];
    }
}
