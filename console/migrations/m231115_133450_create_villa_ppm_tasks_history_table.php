<?php

use common\models\LocationEquipments;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%villa_ppm_tasks_history}}`.
 */
class m231115_133450_create_villa_ppm_tasks_history_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "villa_ppm_tasks_history";
    }

    public function columns()
    {
        return [
            'task_id' => $this->foreignKey('villa_ppm_tasks', 'id', $this->integer()),
            'asset_id' => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            'ppm_service_id' => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            'frequency' => $this->integer(),
            'remarks' => $this->string(),
        ];
    }
}
