<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%profession}}`.
 */
class m230802_163257_create_profession_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "profession";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'description' => $this->text(),
        ];
    }
}
