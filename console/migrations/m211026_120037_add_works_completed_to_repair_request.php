<?php

use yii\db\Migration;

/**
 * Class m211026_120037_add_works_completed_to_repair_request
 */
class m211026_120037_add_works_completed_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "works_completed", $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "works_completed");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211026_120037_add_works_completed_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
