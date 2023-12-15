<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%location_equipments}}`.
 */
class m230830_070852_create_location_equipments_table extends CreateTableMigration
{
    // public $skipTimeStamps = true;
    // public $skipBleameables = true;
    // public $skipStatus = true;

    public function getTableName()
    {
        return "location_equipments";
    }

    public function columns()
    {
        return [
            "location_id" => $this->foreignKey('location', 'id'),
            "equipment_id" => $this->foreignKey('equipment', 'id'),
            "code" => $this->string(),
            "value" => $this->text(),
        ];
    }
}
