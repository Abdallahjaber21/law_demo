<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `line_item`.
 */
class m191025_074243_create_line_item_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "line_item";
    }

    public function columns()
    {
        return [
            "repair_request_id" => $this->foreignKey("repair_request", "id"),
            "object_code_id" => $this->foreignKey("object_code", "id"),
            "cause_code_id" => $this->foreignKey("cause_code", "id"),
            "damage_code_id" => $this->foreignKey("damage_code", "id"),
            "manufacturer_id" => $this->foreignKey("manufacturer", "id"),
        ];
    }
}
