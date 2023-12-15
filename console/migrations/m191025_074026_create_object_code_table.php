<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `object_code`.
 */
class m191025_074026_create_object_code_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "object_code";
    }

    public function columns()
    {
        return [
            "object_category_id" => $this->foreignKey("object_category", "id"),
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
