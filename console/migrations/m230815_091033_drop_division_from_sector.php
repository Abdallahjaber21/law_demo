<?php

use yii\db\Migration;

/**
 * Class m230815_091033_drop_division_from_sector
 */
class m230815_091033_drop_division_from_sector extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey("fk_sector_division_id", "sector");
        $this->dropIndex("fk_sector_division_id_idx", "sector");
        $this->dropColumn("sector", "division_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
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
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_091033_drop_division_from_sector cannot be reverted.\n";

        return false;
    }
    */
}
