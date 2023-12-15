<?php

use common\models\EquipmentCaValue;
use common\models\LocationEquipments;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m230904_063122_add_location_equipments_id_to_ca_value_table
 */
class m230904_063122_add_location_equipments_id_to_ca_value_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey(EquipmentCaValue::tableName(), 'location_equipment_id', $this->integer(), LocationEquipments::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(EquipmentCaValue::tableName(), 'location_equipment_id', LocationEquipments::tableName(), 'id');
    }
}
