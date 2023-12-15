<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `customer`.
 */
class m191023_133221_create_customer_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "customer";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string(),
            "address" => $this->string(),
            "phone" => $this->string(),
        ];
    }
}
