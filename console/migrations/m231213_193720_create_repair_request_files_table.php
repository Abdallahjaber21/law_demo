<?php

use common\models\RepairRequest;
use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%repair_request_files}}`.
 */
class m231213_193720_create_repair_request_files_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "repair_request_files";
    }

    public function columns()
    {
        return [
            'repair_request_id' => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            'old_file' => $this->string(),
            'new_file' => $this->string(),
            'type' => $this->string(),

        ];
    }
}
