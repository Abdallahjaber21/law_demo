<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `technician_location`.
 */
class m191029_123730_create_technician_location_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "technician_location";
    }

    public function columns()
    {
        return [
            "technician_id" => $this->foreignKey("technician", "id"),
            "latitude" => $this->string(),
            "longitude" => $this->string(),
        ];
    }
}
