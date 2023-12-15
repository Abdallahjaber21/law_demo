<?php

use yii\db\Migration;

/**
 * Class m230810_061818_add_division_to_main_sector
 */
class m230810_061818_add_division_to_main_sector extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("main_sector", "division_id", $this->integer());
        $this->createIndex("fk_main_sector_division_id_idx", "main_sector", "division_id");
        $this->addForeignKey(
            'fk_main_sector_division_id',
            'main_sector',
            'division_id',
            'division',
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
        $this->dropForeignKey('fk_main_sector_division_id', 'main_sector');
        $this->dropIndex('fk_main_sector_division_id_idx', 'main_sector');
        $this->dropColumn('main_sector', 'division_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230810_061818_add_division_to_main_sector cannot be reverted.\n";

        return false;
    }
    */
}
