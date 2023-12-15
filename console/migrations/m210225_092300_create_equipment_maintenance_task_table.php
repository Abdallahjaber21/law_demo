<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `maintenance_task`.
 */
class m210225_092300_create_equipment_maintenance_task_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "equipment_maintenance_barcodes";
    }

    public function columns()
    {
        return [
            'equipment_id' => $this->foreignKey("equipment", "id"),
            "location" => $this->string(),
            "barcode" => $this->string(),
        ];
    }
}
