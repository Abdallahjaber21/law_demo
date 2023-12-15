<?php

use yii\db\Migration;

/**
 * Class m230802_180007_add_columns_to_user
 */
class m230802_180007_add_columns_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn("user", "division_id", $this->integer());
        $this->createIndex("fk_user_division_id_idx", "user", "division_id");
        $this->addForeignKey(
            'fk_user_division',
            'user',
            'division_id',
            'division',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("user", "profession_id", $this->integer());
        $this->createIndex("fk_user_profession_id_idx", "user", "profession_id");
        $this->addForeignKey(
            'fk_user_profession',
            'user',
            'profession_id',
            'profession',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("user", "badge_number", $this->string(50)->null());
        $this->addColumn("user", "description", $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_user_division_id", "user");
        $this->dropIndex("fk_user_division_id_idx", "user");
        $this->dropColumn("user", "division_id");
        $this->dropForeignKey("fk_user_profession_id", "user");
        $this->dropIndex("fk_user_profession_id_idx", "user");
        $this->dropColumn("user", "profession_id");
        $this->dropColumn("user", "badge_number");
        $this->dropColumn("user", "description");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_180007_add_columns_to_user cannot be reverted.\n";

        return false;
    }
    */
}
