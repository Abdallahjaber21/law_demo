<?php

use console\models\AccountTableMigration;

/**
 * Handles the creation of table `technician`.
 */
class m191025_060116_create_technician_table extends AccountTableMigration
{

    public function getTableName()
    {
        return "technician";
    }
}
