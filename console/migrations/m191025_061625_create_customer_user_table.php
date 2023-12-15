<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `customer_user`.
 */
class m191025_061625_create_customer_user_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "customer_user";
    }

    public function columns()
    {
        return [
            "customer_id" => $this->foreignKey("customer", "id"),
            "user_id" => $this->foreignKey("user", "id")
        ];
    }
}
