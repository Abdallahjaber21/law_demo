<?php

/**
 * Handles the creation of table `{{%user_removal_request}}`.
 */
class m210702_105429_create_user_removal_request_table extends \console\models\CreateTableMigration
{

    public function getTableName()
    {
        return "removal_request";
    }

    public function columns()
    {
        return [
            "requester_id"     => $this->foreignKey("user", "id", $this->integer()),
            "user_location_id" => $this->foreignKey("user_location", "id", $this->integer()),
            "reason"           => $this->string(),
        ];
    }
}
