<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `repair_request_maintenance_task`.
 */
class m200623_054833_create_repair_request_maintenance_task_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "repair_request_maintenance_task";
    }

    public function columns()
    {
        return [
            "repair_request_id" => $this->foreignKey("repair_request", "id", $this->integer()->notNull()),
            "maintenance_task_group_id" => $this->foreignKey("maintenance_task_group", "id", $this->integer()->notNull()),
            "checked" => $this->boolean()->defaultValue(false),
        ];
    }

    public function safeUp()
    {
        parent::safeUp(); // TODO: Change the autogenerated stub
        $this->createIndex("idx_unique_request_task", "repair_request_maintenance_task",
            ['repair_request_id', 'maintenance_task_group_id'], true);

    }
}
