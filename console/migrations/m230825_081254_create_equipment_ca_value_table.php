<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%equipment_ca_value}}`.
 */
class m230825_081254_create_equipment_ca_value_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "equipment_ca_value";
    }
    public function columns()
    {
        return [
            'equipment_ca_id' => $this->foreignKey("equipment_ca", "id"),
            'equipment_id' => $this->foreignKey("equipment", "id"),
            'value' => $this->string(),


        ];
    }
}
