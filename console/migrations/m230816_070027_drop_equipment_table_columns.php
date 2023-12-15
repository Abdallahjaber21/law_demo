<?php

use yii\db\Migration;

/**
 * Class m230816_070027_drop_equipment_table_columns
 */
class m230816_070027_drop_equipment_table_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_equipment_contract_id__contract_id', 'equipment');
        $this->dropIndex('fk_equipment_contract_id__contract_id_idx', 'equipment');
        $this->dropColumn('equipment', 'contract_id');

        // rest of it 
        $this->dropColumn('equipment', 'contract_code');
        $this->dropColumn('equipment', 'manufacturer');
        $this->dropColumn('equipment', 'type');
        $this->dropColumn('equipment', 'equipment_type');
        $this->dropColumn('equipment', 'unit_type');
        $this->dropColumn('equipment', 'temporary_in');
        $this->dropColumn('equipment', 'temporary_out');
        $this->dropColumn('equipment', 'material');
        $this->dropColumn('equipment', 'expire_at');
        $this->dropColumn('equipment', 'actually_in');
        $this->dropColumn('equipment', 'floor');
        $this->dropColumn('equipment', 'zone');
        $this->dropColumn('equipment', 'place');
        $this->dropColumn('equipment', 'details');
        $this->dropColumn('equipment', 'quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("equipment", "contract_id", $this->integer());
        $this->createIndex("fk_equipment_contract_id__contract_id_idx", "equipment", "contract_id");
        $this->addForeignKey(
            'fk_equipment_contract_id__contract_id',
            'equipment',
            'contract_id',
            'profession',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // rest of it
        $this->addColumn('equipment', 'contract_code', $this->string(255));
        $this->addColumn('equipment', 'manufacturer', $this->string(255));
        $this->addColumn('equipment', 'equipment_type', $this->string(255));
        $this->addColumn('equipment', 'unit_type', $this->string(255));
        $this->addColumn('equipment', 'material', $this->string(255));
        $this->addColumn('equipment', 'floor', $this->string(255));
        $this->addColumn('equipment', 'zone', $this->string(255));
        $this->addColumn('equipment', 'place', $this->string(255));
        $this->addColumn('equipment', 'details', $this->string(255));
        $this->addColumn('equipment', 'type', $this->integer(11));
        $this->addColumn('equipment', 'quantity', $this->integer(11));
        $this->addColumn('equipment', 'temporary_in', $this->tinyInteger(11));
        $this->addColumn('equipment', 'temporary_out', $this->tinyInteger(11));
        $this->addColumn('equipment', 'expire_at', $this->dateTime());
        $this->addColumn('equipment', 'actually_in', $this->tinyInteger(11));
    }
}
