<?php

use console\models\CreateTableMigration;

class m230926_092613_create_plant_ppm_tasks_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "plant_ppm_tasks";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'task_type' => $this->integer(),
            'occurence_value' => $this->integer(),
            'meter_type' => $this->integer(),
        ];
    }
}
