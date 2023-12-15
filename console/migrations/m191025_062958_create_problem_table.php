<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `problem`.
 */
class m191025_062958_create_problem_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "problem";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string()
        ];
    }
}
