<?php

use yii\db\Migration;

/**
 * Class m211101_141709_add_phone_number_to_repair_request
 */
class m211101_141709_add_phone_number_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "reported_by_phone", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "reported_by_phone");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211101_141709_add_phone_number_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
