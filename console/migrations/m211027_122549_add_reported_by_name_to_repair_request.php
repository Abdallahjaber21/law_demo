<?php

use yii\db\Migration;

/**
 * Class m211027_122549_add_reported_by_name_to_repair_request
 */
class m211027_122549_add_reported_by_name_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "reported_by_name", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "reported_by_name");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211027_122549_add_reported_by_name_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
