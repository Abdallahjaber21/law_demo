<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%maintenance_report}}`.
 */
class m210616_084531_create_maintenance_report_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "maintenance_report";
    }

    public function columns()
    {
        return [
            'location_id'   => $this->foreignKey("location", "id", $this->integer()),
            'technician_id' => $this->foreignKey("technician", "id", $this->integer()),
            'month'         => $this->integer(),
            'year'          => $this->integer(),
            'report'        => $this->string(),
            'random_token'  => $this->string(),
        ];
    }
}
