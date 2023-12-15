<?php

use yii\db\Migration;

/**
 * Class m211018_084515_add_confirmed_equipment_to_repair_request
 */
class m211018_084515_add_confirmed_equipment_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "confirmed_equipment", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "confirmed_equipment");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211018_084515_add_confirmed_equipment_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
