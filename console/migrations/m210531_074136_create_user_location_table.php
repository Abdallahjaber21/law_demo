<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%user_location}}`.
 */
class m210531_074136_create_user_location_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "user_location";
    }

    public function columns()
    {
        return [
            'user_id'     => $this->foreignKey("user", "id", $this->integer()),
            'location_id' => $this->foreignKey("location", "id", $this->integer()),
            'role'        => $this->integer()->defaultValue(10),
        ];
    }
}
