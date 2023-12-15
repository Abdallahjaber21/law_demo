<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%shift}}`.
 */
class m230802_162509_create_shift_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "shift";
    }
    public function columns()
    {
        return [
            'name' => $this->string(),
            'from_hour' => $this->Time(),
            'to_hour' => $this->Time(),
            'description' => $this->text(),
        ];
    }
}
