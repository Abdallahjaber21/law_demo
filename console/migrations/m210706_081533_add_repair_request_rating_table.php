<?php

use console\models\CreateTableMigration;

/**
 * Class m210706_081533_add_repair_request_rating_table
 */
class m210706_081533_add_repair_request_rating_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "repair_request_rating";
    }

    public function columns()
    {
        return [
            'repair_request_id' => $this->foreignKey("repair_request", "id", $this->integer()),
            'user_id'           => $this->foreignKey("user", "id", $this->integer()),
            'rating'            => $this->integer(),
            'comment'           => $this->string(),
        ];
    }
}
