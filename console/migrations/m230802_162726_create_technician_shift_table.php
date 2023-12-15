<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%technician_shift}}`.
 */
class m230802_162726_create_technician_shift_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "technician_shift";
    }

    public function columns()
    {
        return [
            'technician_id' => $this->foreignKey("technician", 'id', $this->integer()->notNull()),
            'shift_id'     => $this->foreignKey("shift", 'id', $this->integer()->notNull()),
            'date' => $this->date(),
            'description' => $this->text(),

        ];
    }
}
