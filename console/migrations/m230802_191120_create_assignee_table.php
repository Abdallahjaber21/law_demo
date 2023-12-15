<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%assignee}}`.
 */
class m230802_191120_create_assignee_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "assignee";
    }
    public function columns()
    {
        return [
            'repair_request_id' => $this->foreignKey("repair_request", "id"),
            'user_id' => $this->foreignKey("technician", "id"),
            'description' => $this->text(),
        ];
    }
}
