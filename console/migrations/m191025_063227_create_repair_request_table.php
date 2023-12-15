<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `repair_request`.
 */
class m191025_063227_create_repair_request_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "repair_request";
    }

    public function columns()
    {
        return [
            "technician_id" => $this->foreignKey("technician", "id"),
            "user_id" => $this->foreignKey("user", "id"),
            "equipment_id" => $this->foreignKey("equipment", "id"),

            "person_trapped" => $this->boolean(),
            "system_operational" => $this->boolean(),

            "schedule" => $this->integer(),
            "extra_cost" => $this->double(),

            "type" => $this->integer(),//Request or maintenance

            "requested_at" => $this->dateTime(),
            "scheduled_at" => $this->dateTime(),
            "informed_at" => $this->dateTime(),
            "arrived_at" => $this->dateTime(),
            "departed_at" => $this->dateTime(),
        ];
    }
}
