<?php

use common\models\EquipmentType;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%mall_ppm_tasks}}`.
 */
class m230926_092613_create_mall_ppm_tasks_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "mall_ppm_tasks";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'frequency' => $this->integer(),
            'equipment_type_id' => $this->foreignKey(EquipmentType::tableName(), 'id', $this->integer()),
        ];
    }
}
