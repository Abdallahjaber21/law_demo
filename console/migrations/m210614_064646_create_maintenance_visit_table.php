<?php

/**
 * Handles the creation of table `{{%maintenance_visit}}`.
 */
class m210614_064646_create_maintenance_visit_table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return "maintenance_visit";
    }

    public function columns()
    {
        return [
            'location_id'   => $this->foreignKey("location", "id", $this->integer()),
            'technician_id' => $this->foreignKey("technician", "id", $this->integer()),
            'checked_in'    => $this->dateTime(),
            'status'        => $this->integer(),
            'checked_out'   => $this->dateTime(),
        ];
    }
}
