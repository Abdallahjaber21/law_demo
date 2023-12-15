<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m230802_170736_create_category_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "category";
    }
    public function columns()
    {
        return [
            'name' => $this->string(),
            'parent_id' => $this->foreignKey("category", 'id'),
            'description' => $this->text(),
        ];
    }
}
