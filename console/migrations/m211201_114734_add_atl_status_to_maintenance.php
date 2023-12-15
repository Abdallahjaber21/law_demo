<?php

use yii\db\Migration;

/**
 * Class m211201_114734_add_atl_status_to_maintenance
 */
class m211201_114734_add_atl_status_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "atl_status", $this->integer());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "atl_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211201_114734_add_atl_status_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
