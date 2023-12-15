<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%equipment_ca}}`.
 */
class m230825_080732_create_equipment_ca_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "equipment_ca";
    }
    public function columns()
    {
        return [
            'division_id' => $this->foreignKey("division", "id"),
            'name' => $this->string(),


        ];
    }
}
