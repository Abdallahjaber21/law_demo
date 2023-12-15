<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `maintenance_task`.
 */
class m200619_074827_create_maintenance_task_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "maintenance_task";
    }

    public function columns()
    {
        return [
            'maintenance_task_group_id' => $this->foreignKey("maintenance_task_group", "id"),
            "code" => $this->string()->unique(),
            "name" => $this->string(),
            "task_order" => $this->integer()->defaultValue(99),
            "duration" => $this->double(),
            "m_1_a" => $this->boolean()->defaultValue( true),
            "m_2_a" => $this->boolean()->defaultValue( true),
            "m_3_a" => $this->boolean()->defaultValue( true),
            "m_4_a" => $this->boolean()->defaultValue( true),
            "m_5_a" => $this->boolean()->defaultValue( true),
            "m_6_a" => $this->boolean()->defaultValue( true),
            "m_7_a" => $this->boolean()->defaultValue( true),
            "m_8_a" => $this->boolean()->defaultValue( true),
            "m_9_a" => $this->boolean()->defaultValue( true),
           "m_10_a" => $this->boolean()->defaultValue( true),
           "m_11_a" => $this->boolean()->defaultValue( true),
           "m_12_a" => $this->boolean()->defaultValue( true),
            "m_1_b" => $this->boolean()->defaultValue(false),
            "m_2_b" => $this->boolean()->defaultValue(false),
            "m_3_b" => $this->boolean()->defaultValue(false),
            "m_4_b" => $this->boolean()->defaultValue(false),
            "m_5_b" => $this->boolean()->defaultValue(false),
            "m_6_b" => $this->boolean()->defaultValue(false),
            "m_7_b" => $this->boolean()->defaultValue(false),
            "m_8_b" => $this->boolean()->defaultValue(false),
            "m_9_b" => $this->boolean()->defaultValue(false),
           "m_10_b" => $this->boolean()->defaultValue(false),
           "m_11_b" => $this->boolean()->defaultValue(false),
           "m_12_b" => $this->boolean()->defaultValue(false),
        ];
    }
}
