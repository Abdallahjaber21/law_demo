<?php

/**
 * Handles the creation of table `{{%barcode_scan_}}`.
 */
class m210614_064132_create_barcode_scan__table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return "barcode_scan";
    }

    public function columns()
    {
        return [
            "maintenance_id" => $this->foreignKey("maintenance", "id", $this->integer()),
            "barcode_id"     => $this->foreignKey("equipment_maintenance_barcodes", "id", $this->integer()),
        ];
    }
}
