<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `completed_maintenance_task_table`.
 */
class m210225_112300_create_completed_maintenance_task_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "completed_maintenance_task";
    }

    public function columns()
    {
        return [
            "repair_request_id" => $this->foreignKey("repair_request", "id", $this->integer()->notNull()),
            "equipment_maintenance_barcode_id" => $this->foreignKey("equipment_maintenance_barcodes", "id", $this->integer()->notNull()),
        ];
    }
}
