<?php

/**
 * Handles the creation of table `{{%maintenance}}`.
 */
class m210614_060818_create_maintenance_table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return "maintenance";
    }

    public function columns()
    {
        return [
            'equipment_id'         => $this->foreignKey("equipment", 'id', $this->integer()),
            'location_id'          => $this->foreignKey("location", 'id', $this->integer()),
            'technician_id'        => $this->foreignKey("technician", 'id', $this->integer()),
            'status'               => $this->integer(),
            'year'    => $this->integer(),
            'month'    => $this->integer(),
            'random_token'         => $this->string(),
            'note'                 => $this->text(),
            'customer_name'        => $this->string(),
            'customer_signature'   => $this->string(),
            'technician_signature' => $this->string(),
        ];
    }
}
