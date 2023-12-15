<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%log}}`.
 */
class m230802_190516_create_log_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "log";
    }
    public function columns()
    {
        return [
            'technician_id' => $this->foreignKey("technician", "id"),
            'repair_request_id' => $this->foreignKey("repair_request", "id"),
            'gallery_id' => $this->foreignKey("gallery", "id"),
            'type' => $this->string(),
            'note' => $this->text(),
            'date_time' => $this->dateTime(),
            'description' => $this->text(),


        ];
    }
}
