<?php

use yii\db\Migration;

/**
 * Class m210917_054502_add_atl_note_to_repair_request
 */
class m210917_054502_add_atl_note_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "atl_note", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "atl_note");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210917_054502_add_atl_note_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
