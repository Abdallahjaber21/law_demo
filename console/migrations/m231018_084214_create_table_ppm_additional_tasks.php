<?php

use common\models\EquipmentType;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231018_084214_create_table_ppm_additional_tasks
 */
class m231018_084214_create_table_ppm_additional_tasks extends CreateTableMigration
{
    public function getTableName()
    {
        return "ppm_additional_tasks";
    }

    public function columns()
    {
        return [
            "equipment_type_id" => $this->foreignKey(EquipmentType::tableName(), 'id', $this->integer()),
            "name" => $this->string(),
            "service" => $this->string(),
        ];
    }
}
