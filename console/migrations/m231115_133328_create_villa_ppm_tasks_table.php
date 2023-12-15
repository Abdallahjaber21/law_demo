<?php

use common\models\EquipmentType;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%villa_ppm_tasks}}`.
 */
class m231115_133328_create_villa_ppm_tasks_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "villa_ppm_tasks";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'frequency' => $this->integer(),
            'equipment_type_id' => $this->foreignKey(EquipmentType::tableName(), 'id', $this->integer()),
            'occurence_value' => $this->integer(),
        ];
    }
}
