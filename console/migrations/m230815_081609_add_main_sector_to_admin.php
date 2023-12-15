<?php

use yii\db\Migration;

/**
 * Class m230815_081609_add_main_sector_to_admin
 */
class m230815_081609_add_main_sector_to_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("admin", "main_sector_id", $this->integer());
        $this->createIndex("fk_admin_main_sector_id_idx", "sector", "main_sector_id");
        $this->addForeignKey(
            'fk_admin_main_sector_id',
            'admin',
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

        $this->dropForeignKey("fk_admin_main_sector_id", "admin");
        $this->dropIndex("fk_admin_main_sector_id_idx", "admin");
        $this->dropColumn("admin", "main_sector_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_081609_add_main_sector_to_admin cannot be reverted.\n";

        return false;
    }
    */
}
