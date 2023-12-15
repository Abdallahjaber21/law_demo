<?php

use yii\db\Migration;

/**
 * Class m211026_061617_add_temporary_in_out_to_contract
 */
class m211026_061617_add_temporary_in_out_to_contract extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("contract", "temporary_in", $this->boolean()->defaultValue(false));
        $this->addColumn("contract", "temporary_out", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("contract", "temporary_in");
        $this->dropColumn("contract", "temporary_out");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211026_061617_add_temporary_in_out_to_contract cannot be reverted.\n";

        return false;
    }
    */
}
