<?php

use yii\db\Migration;

/**
 * Class m210705_104116_add_visit_id_to_barcode_scan
 */
class m210705_104116_add_visit_id_to_barcode_scan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("barcode_scan", "visit_id", $this->integer());
        $this->createIndex("idx_barcode_scan_visit_id", "barcode_scan", "visit_id", false);
        $this->addForeignKey("fk_barcode_scan_visit_id", "barcode_scan", "visit_id", "maintenance_visit", "id", "CASCADE", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_barcode_scan_visit_id", "barcode_scan");
        $this->dropIndex("idx_barcode_scan_visit_id", "barcode_scan");
        $this->dropColumn("barcode_scan", "visit_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210705_104116_add_visit_id_to_barcode_scan cannot be reverted.\n";

        return false;
    }
    */
}
