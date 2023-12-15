<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%main_sector}}`.
 */
class m230802_165258_create_main_sector_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "main_sector";
    }
    public function columns()
    {
        return [
            'name' => $this->string(),
            'description' => $this->text(),
        ];
    }
}
