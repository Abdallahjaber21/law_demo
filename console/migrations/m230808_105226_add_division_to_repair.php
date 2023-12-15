<?php

use yii\db\Migration;

/**
 * Class m230808_105226_add_division_to_repair
 */
class m230808_105226_add_division_to_repair extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "division_id", $this->integer());
        $this->createIndex("fk_repair_request_division_id_idx", "repair_request", "division_id");
        $this->addForeignKey(
            'fk_repair_request_division',
            'repair_request',
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
        $this->dropForeignKey("fk_repair_request_division", "repair_request");
        $this->dropIndex("fk_repair_request_division_id_idx", "repair_request");
        $this->dropColumn("repair_request", "division_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_105226_add_division_to_repair cannot be reverted.\n";

        return false;
    }
    */
}
