<?php

use yii\db\Migration;

/**
 * Class m211221_083112_add_num_of_barcodes_to_maintenance
 */
class m211221_083112_add_num_of_barcodes_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "number_of_barcodes", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "number_of_barcodes");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_083112_add_num_of_barcodes_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
