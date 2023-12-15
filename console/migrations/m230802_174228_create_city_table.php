<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m230802_174228_create_city_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "city";
    }
    public function columns()
    {
        return [
            'state_id' => $this->foreignKey("state", "id"),
            'name' => $this->string(),
        ];
    }
}
