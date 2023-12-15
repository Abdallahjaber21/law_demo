<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%state}}`.
 */
class m230802_174138_create_state_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "state";
    }
    public function columns()
    {
        return [
            'country_id' => $this->foreignKey("country", "id"),
            'name' => $this->string(),
        ];
    }
}
