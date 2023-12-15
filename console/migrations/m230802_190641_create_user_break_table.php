<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%user_break}}`.
 */
class m230802_190641_create_user_break_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "user_break";
    }
    public function columns()
    {
        return [
            'technician_id' => $this->foreignKey("technician", "id"),
            'repair_request_id' => $this->foreignKey("repair_request", "id"),
            'date' => $this->dateTime(),
            'from_break' => $this->string(),
            'to_break' => $this->string(),
            'description' => $this->text(),


        ];
    }
}
