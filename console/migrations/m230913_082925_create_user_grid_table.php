<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%user_grid}}`.
 */
class m230913_082925_create_user_grid_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "user_grid";
    }

    public function columns()
    {
        return [
            "user_id" => $this->foreignKey('admin', 'id'),
            "page_id" => $this->string(),
            "value" => $this->text(),
        ];
    }
}