<?php

use yii\db\Migration;

/**
 * Handles adding default_technician_id to table `sector`.
 */
class m201023_082644_add_default_technician_id_column_to_sector_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sector', 'default_technician_id', $this->integer());
        $this->createIndex("idx_sector_default_technician_id", 'sector', 'default_technician_id');
        $this->addForeignKey("fk_sector_default_technician_id", 'sector', 'default_technician_id',
            'technician', 'id', 'SET NULL', 'SET NULL');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_sector_default_technician_id", 'sector');
        $this->dropIndex("idx_sector_default_technician_id", 'sector');
        $this->dropColumn('sector', 'default_technician_id');
    }
}
