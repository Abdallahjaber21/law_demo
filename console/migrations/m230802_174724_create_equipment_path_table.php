<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%equipment_path}}`.
 */
class m230802_174724_create_equipment_path_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "equipment_path";
    }
    public function columns()
    {
        return [
            'name' => $this->string(),
            'description' => $this->text(),
        ];
    }
}
