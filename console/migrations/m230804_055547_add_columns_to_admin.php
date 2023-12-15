<?php

use yii\db\Migration;

/**
 * Class m230804_055547_add_columns_to_admin
 */
class m230804_055547_add_columns_to_admin extends Migration
{
    public function safeUp()
    {
        $this->addColumn("admin", "division_id", $this->integer());
        $this->createIndex("fk_admin_division_id_idx", "admin", "division_id");
        $this->addForeignKey(
            'fk_admin_division',
            'admin',
            'division_id',
            'division',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addColumn("admin", "profession_id", $this->integer());
        $this->createIndex("fk_admin_profession_id_idx", "admin", "profession_id");
        $this->addForeignKey(
            'fk_admin_profession',
            'admin',
            'profession_id',
            'profession',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("admin", "badge_number", $this->string(50)->null());
        $this->addColumn("admin", "description", $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_admin_division", "admin");
        $this->dropIndex("fk_admin_division_id_idx", "admin");
        $this->dropColumn("admin", "division_id");
        $this->dropForeignKey("fk_admin_profession_id", "admin");
        $this->dropIndex("fk_admin_profession_id_idx", "admin");
        $this->dropColumn("admin", "profession_id");
        $this->dropColumn("admin", "badge_number");
        $this->dropColumn("admin", "description");
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230804_055547_add_columns_to_admin cannot be reverted.\n";

        return false;
    }
    */
}
