<?php

use yii\db\Migration;

/**
 * Class m230802_172530_add_columns_to_sector
 */
class m230802_172530_add_columns_to_sector extends Migration
{
    public function safeUp()
    {
        $this->addColumn("sector", "main_sector_id", $this->integer());
        $this->createIndex("fk_sector_main_sector_id_idx", "sector", "main_sector_id");
        $this->addForeignKey(
            'fk_sector_main_sector_id',
            'sector',
            'main_sector_id',
            'main_sector',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("sector", "division_id", $this->integer());
        $this->createIndex("fk_sector_division_id_idx", "sector", "division_id");
        $this->addForeignKey(
            'fk_sector_division_id',
            'sector',
            'division_id',
            'division',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("sector", "description", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_sector_main_sector_id", "sector");
        $this->dropIndex("fk_sector_main_sector_id_idx", "sector");
        $this->dropColumn("sector", "main_sector_id");
        $this->dropForeignKey("fk_sector_division_id", "sector");
        $this->dropIndex("fk_sector_division_id_idx", "sector");
        $this->dropColumn("sector", "division_id");
        $this->dropColumn("sector", "description");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_172530_add_columns_to_sector cannot be reverted.\n";

        return false;
    }
    */
}
