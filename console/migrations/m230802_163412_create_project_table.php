<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%project}}`.
 */
class m230802_163412_create_project_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "project";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'location_id' => $this->foreignKey("location", 'id', $this->integer()->null()),
            'description' => $this->text(),
        ];
    }
}
