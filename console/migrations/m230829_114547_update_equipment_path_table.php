<?php

use yii\db\Migration;

/**
 * Class m230829_114547_update_equipment_path_table
 */
class m230829_114547_update_equipment_path_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment_path", "location_id", $this->integer()->after('equipment_id'));
        $this->createIndex("fk_equipment_path_location_id_idx", "equipment_path", "location_id");
        $this->addForeignKey(
            'fk_equipment_path_location_id',
            'equipment_path',
            'location_id',
            'location',
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
        $this->dropForeignKey("fk_equipment_path_location_id", "equipment_path");
        $this->dropIndex("fk_equipment_path_location_id_idx", "equipment_path");
        $this->dropColumn("equipment_path", "location_id");
    }
}
