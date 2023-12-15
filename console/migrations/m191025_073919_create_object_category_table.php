<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `object_category`.
 */
class m191025_073919_create_object_category_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "object_category";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
