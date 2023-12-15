<?php

use console\models\AccountTableMigration;

/**
 * Handles the creation of table `user`.
 */
class m191023_135038_create_user_table extends AccountTableMigration
{

    public function getTableName()
    {
        return "user";
    }
}
