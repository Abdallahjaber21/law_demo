<?php

use common\models\EquipmentType;
use yii\db\Migration;

/**
 * Class m231129_102943_add_reference_value_to_equipment_type_table
 */
class m231129_102943_add_reference_value_to_equipment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(EquipmentType::tableName(), 'reference_value', $this->integer()->after('alt_meter_type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(EquipmentType::tableName(), 'reference_value');
    }
}
