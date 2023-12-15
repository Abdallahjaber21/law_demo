<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m230802_174050_create_country_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "country";
    }
    public function columns()
    {
        return [
            'name' => $this->string(),
            'country_code' => $this->integer(10),
            'currency' => $this->string(20),
        ];
    }
}
