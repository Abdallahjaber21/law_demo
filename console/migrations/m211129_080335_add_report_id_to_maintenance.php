<?php

use yii\db\Migration;

/**
 * Class m211129_080335_add_report_id_to_maintenance
 */
class m211129_080335_add_report_id_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "report_id", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "report_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_080335_add_report_id_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
