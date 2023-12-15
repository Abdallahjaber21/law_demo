<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%user_audit}}`.
 */
class m230919_133242_create_user_audit_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "user_audit";
    }

    public function columns()
    {

        return [
            "user_id" => $this->foreignKey('admin', 'id'),
            "class_id" => $this->integer(),
            "entity_row_id" => $this->integer(),
            'action' => $this->text(),
            'old_value' => $this->text(),
            'new_value' => $this->text(),
        ];
    }
}
