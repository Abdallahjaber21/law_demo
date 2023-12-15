<?php

use common\models\LocationEquipments;
use yii\db\Migration;

/**
 * Class m230926_081232_add_fields_to_location_equipment_table
 */
class m230926_081232_add_fields_to_location_equipment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(LocationEquipments::tableName(), 'meter_value', $this->integer()->after('value'));
        $this->addColumn(LocationEquipments::tableName(), 'meter_damaged', $this->boolean()->after('meter_value'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(LocationEquipments::tableName(), 'meter_value');
        $this->dropColumn(LocationEquipments::tableName(), 'meter_damaged');
    }
}
