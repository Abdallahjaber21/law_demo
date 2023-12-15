<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `location`.
 */
class m191023_133243_create_location_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "location";
    }

    public function columns()
    {
        return [
            "sector_id" => $this->foreignKey("sector", "id"),
            "customer_id" => $this->foreignKey("customer", "id"),
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
