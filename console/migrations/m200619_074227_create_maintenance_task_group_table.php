<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `maintenance_task_group`.
 */
class m200619_074227_create_maintenance_task_group_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "maintenance_task_group";
    }

    public function columns()
    {
        return [
            "equipment_type"=> $this->integer(),
            "code" => $this->string()->unique(),
            "name" => $this->string(),
            "group_order" => $this->integer()->defaultValue(99),
        ];
    }
}
