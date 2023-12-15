<?php

use yii\db\Migration;

/**
 * Class m230816_100715_modify_equipment_path_table
 */
class m230816_100715_modify_equipment_path_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn("equipment_path", "segment_path_id", $this->integer()->after('name'));
        $this->createIndex("fk_equipment_path_segment_path_id_idx", "equipment_path", "segment_path_id");
        $this->addForeignKey(
            'fk_equipment_path_segment_path_id',
            'equipment_path',
            'segment_path_id',
            'segment_path',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("equipment_path", "equipment_id", $this->integer()->after('segment_path_id'));
        $this->createIndex("fk_equipment_path_equipment_id_idx", "equipment_path", "equipment_id");
        $this->addForeignKey(
            'fk_equipment_path_equipment_id',
            'equipment_path',
            'equipment_id',
            'equipment',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn('equipment_path', 'value', 'LONGTEXT');


        $this->dropColumn('equipment_path', 'name');

        $this->dropForeignKey('fk_equipment_equipment_path_id', 'equipment');
        $this->dropIndex('fk_equipment_equipment_path_id_idx', 'equipment');
        $this->dropColumn('equipment', 'equipment_path_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_equipment_path_segment_path_id', 'equipment_path');
        $this->dropIndex('fk_equipment_path_segment_path_id_idx', 'equipment_path');
        $this->dropColumn('equipment_path', 'segment_path_id');

        $this->dropForeignKey('fk_equipment_path_equipment_id', 'equipment_path');
        $this->dropIndex('fk_equipment_path_equipment_id_idx', 'equipment_path');
        $this->dropColumn('equipment_path', 'equipment_id');

        $this->dropColumn('equipment_path', 'value');

        $this->addColumn('equipment_path', 'name', $this->string(255));

        // Equipment table
        $this->addColumn("equipment", "equipment_path_id", $this->integer());
        $this->createIndex("fk_equipment_equipment_path_id_idx", "equipment", "equipment_path_id");
        $this->addForeignKey(
            'fk_equipment_equipment_path_id',
            'equipment',
            'equipment_path_id',
            'equipment_path',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
