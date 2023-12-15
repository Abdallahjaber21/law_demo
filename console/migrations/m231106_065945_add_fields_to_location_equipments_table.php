<?php

use yii\db\Migration;

use common\models\LocationEquipments;

/**
 * Class m231106_065945_add_fields_to_location_equipments_table
 */
class m231106_065945_add_fields_to_location_equipments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(LocationEquipments::tableName(), 'chassie_number', $this->string()->after('remarks'));
        $this->addColumn(LocationEquipments::tableName(), 'motor_fuel_type', $this->integer()->after('chassie_number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(LocationEquipments::tableName(), 'chassie_number');
        $this->dropColumn(LocationEquipments::tableName(), 'motor_fuel_type');
    }

}
