<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%segment_path}}`.
 */
class m230802_174856_create_segment_path_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "segment_path";
    }
    public function columns()
    {
        return [
            'equipment_path_id' => $this->foreignKey("equipment_path", "id"),
            'name' => $this->string(),
            'value' => $this->string(),
        ];
    }
}
