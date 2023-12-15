<?php

use console\models\CreateTableMigration;

/**
 * Class m211203_085323_create_maintenance_log
 */
class m211203_085323_create_maintenance_log extends CreateTableMigration
{
    public function getTableName()
    {
        return 'maintenance_log';
    }

    public function columns()
    {
        return [
            'maintenance_id'  => $this->foreignKey("maintenance", "id", $this->integer()),
            'user_name'   => $this->string(),
            'log_message' => $this->string(),
        ];
    }
}
