<?php

use yii\db\Migration;

/**
 * Class m210617_092349_add_report_attribute_to_repair_request
 */
class m210617_092349_add_report_attribute_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'customer_name', $this->string());
        $this->addColumn('repair_request', 'technician_signature', $this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'customer_name');
        $this->dropColumn('repair_request', 'technician_signature');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210617_092349_add_report_attribute_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
