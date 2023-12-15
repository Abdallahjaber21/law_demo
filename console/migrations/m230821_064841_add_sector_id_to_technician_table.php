<?php

use yii\db\Migration;

/**
 * Class m230821_064841_add_sector_id_to_technician_table
 */
class m230821_064841_add_sector_id_to_technician_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("technician", "sector_id", $this->integer()->after('email'));
        $this->createIndex("fk_technician_sector_id_idx", "technician", "sector_id");
        $this->addForeignKey(
            'fk_technician_sector_id',
            'technician',
            'sector_id',
            'sector',
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
        $this->dropForeignKey('fk_technician_sector_id', 'technician');
        $this->dropIndex('fk_technician_sector_id_idx', 'technician');
        $this->dropColumn('technician', 'sector_id');
    }
}
