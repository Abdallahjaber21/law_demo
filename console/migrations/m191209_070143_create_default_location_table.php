<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `default_location`.
 */
class m191209_070143_create_default_location_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "default_location";
    }

    public function columns()
    {
        return [
            "user_id" => $this->foreignKey("user", "id", $this->integer()->notNull()->unique()),
            "location_id" => $this->foreignKey("location", "id", $this->integer()->notNull()),
        ];
    }
}
