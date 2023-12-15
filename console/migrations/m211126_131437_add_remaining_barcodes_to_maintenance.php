<?php

use yii\db\Migration;

/**
 * Class m211126_131437_add_remaining_barcodes_to_maintenance
 */
class m211126_131437_add_remaining_barcodes_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "remaining_barcodes", $this->integer());
        $this->addColumn("maintenance", "first_scan_at", $this->dateTime());
        $this->addColumn("maintenance", "completed_at", $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "remaining_barcodes");
        $this->dropColumn("maintenance", "first_scan_at");
        $this->dropColumn("maintenance", "completed_at");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211126_131437_add_remaining_barcodes_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
