<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `contract`.
 */
class m191023_133639_create_contract_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "contract";
    }

    public function columns()
    {
        return [
            "customer_id" => $this->foreignKey("customer", "id"),
            "code" => $this->string(),
            "name" => $this->string(),
            "includes_parts" => $this->boolean(),
            "same_day_service" => $this->boolean(),
            "same_day_cost" => $this->double(),
        ];
    }
}
