<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%profession_category}}`.
 */
class m230802_171139_create_profession_category_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "profession_category";
    }
    public function columns()
    {
        return [
            'profession_id' => $this->foreignKey("profession", "id"),
            'category_id' => $this->foreignKey("category", "id"),

        ];
    }
}
