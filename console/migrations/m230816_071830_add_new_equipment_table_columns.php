<?php

use yii\db\Migration;

/**
 * Class m230816_071830_add_new_equipment_table_columns
 */
class m230816_071830_add_new_equipment_table_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "equipment_type_id", $this->integer()->after('location_id'));
        $this->createIndex("fk_equipment_equipment_type_id_idx", "equipment", "equipment_type_id");
        $this->addForeignKey(
            'fk_equipment_equipment_type_id',
            'equipment',
            'equipment_type_id',
            'equipment_type',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_equipment_equipment_type_id", "equipment");
        $this->dropIndex("fk_equipment_equipment_type_id_idx", "equipment");
        $this->dropColumn("equipment", "equipment_type_id");
    }
}
