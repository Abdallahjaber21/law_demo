<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%division}}`.
 */
class m230802_161602_create_division_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "division";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'description' => $this->text(),
        ];
    }
}
