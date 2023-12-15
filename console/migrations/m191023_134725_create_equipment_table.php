<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `equipment`.
 */
class m191023_134725_create_equipment_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "equipment";
    }

    public function columns()
    {
        return [
            "location_id" => $this->foreignKey("location", "id"),
            "contract_id" => $this->foreignKey("contract", "id"),
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
