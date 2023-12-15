<?php

use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m230911_063846_replace_sector_id_with_main_sector_id_in_technician_table
 */
class m230911_063846_replace_sector_id_with_main_sector_id_in_technician_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey("fk_technician_sector_id", "technician");
        // $this->dropIndex("fk_sector_country_id_idx", "technician");
        $this->dropColumn("technician", "sector_id");

        $this->addColumn("technician", "main_sector_id", $this->integer()->after('account_id'));
        $this->createIndex("fk_technician_main_sector_id_idx", "technician", "main_sector_id");
        $this->addForeignKey(
            'fk_technician_main_sector_id',
            'technician',
            'main_sector_id',
            'main_sector',
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
        $this->addColumn("technician", "sector_id", $this->integer()->after('account_id'));
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

        $this->dropForeignKey("fk_technician_main_sector_id", "technician");
        $this->dropIndex("fk_technician_main_sector_id_idx", "technician");
        $this->dropColumn("technician", "main_sector_id");
    }
}
