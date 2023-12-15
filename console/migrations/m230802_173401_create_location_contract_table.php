<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%location_contract}}`.
 */
class m230802_173401_create_location_contract_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "location_contract";
    }
    public function columns()
    {
        return [
            'location_id' => $this->foreignKey("location", "id"),
            'description' => $this->text(),
            'expiry_date' => $this->dateTime(),
            'block_service' => $this->integer()->defaultValue(0),

        ];
    }
}
