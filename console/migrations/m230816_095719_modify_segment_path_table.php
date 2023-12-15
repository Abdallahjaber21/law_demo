<?php

use yii\db\Migration;

/**
 * Class m230816_095719_modify_segment_path_table
 */
class m230816_095719_modify_segment_path_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_segment_path_equipment_path_id__equipment_path_id', 'segment_path');
        $this->dropIndex('fk_segment_path_equipment_path_id__equipment_path_id_idx', 'segment_path');
        $this->dropColumn('segment_path', 'equipment_path_id');

        // Add Columns
        $this->addColumn('segment_path', 'description', $this->string(255));

        $this->addColumn("segment_path", "sector_id", $this->integer()->after('value'));
        $this->createIndex("fk_segment_path_sector_sector_id_idx", "segment_path", "sector_id");
        $this->addForeignKey(
            'fk_segment_path_sector_sector_id',
            'segment_path',
            'sector_id',
            'sector',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->alterColumn('segment_path', 'value', 'LONGTEXT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("segment_path", "equipment_path_id", $this->integer());
        $this->createIndex("fk_segment_path_equipment_path_id__equipment_path_id_idx", "segment_path", "equipment_path_id");
        $this->addForeignKey(
            'fk_segment_path_equipment_path_id__equipment_path_id',
            'segment_path',
            'equipment_path_id',
            'equipment',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // drop columns
        $this->dropColumn('segment_path', 'description');

        $this->dropForeignKey('fk_segment_path_sector_sector_id', 'segment_path');
        $this->dropIndex('fk_segment_path_sector_sector_id_idx', 'segment_path');
        $this->dropColumn('segment_path', 'sector_id');

        $this->alterColumn('segment_path', 'value', $this->string(255));
    }
}
