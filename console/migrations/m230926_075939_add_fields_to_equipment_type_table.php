<?php

use common\models\EquipmentType;
use yii\db\Migration;

/**
 * Class m230926_075939_add_fields_to_equipment_type_table
 */
class m230926_075939_add_fields_to_equipment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(EquipmentType::tableName(), 'meter_type', $this->integer()->after('name'));
        $this->addColumn(EquipmentType::tableName(), 'alt_meter_type', $this->integer()->after('meter_type')->defaultValue(EquipmentType::ALT_METER_TYPE_MONTH));
        $this->addColumn(EquipmentType::tableName(), 'equivalance', $this->string()->after('alt_meter_type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(EquipmentType::tableName(), 'meter_type');
        $this->dropColumn(EquipmentType::tableName(), 'alt_meter_type');
        $this->dropColumn(EquipmentType::tableName(), 'equivalance');
    }
}
