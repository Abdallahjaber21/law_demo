<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `sector`.
 */
class m191023_131415_create_sector_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "sector";
    }

    public function columns()
    {
        return [
            'code' => $this->string(),
            'name' => $this->string(),
        ];
    }
}
