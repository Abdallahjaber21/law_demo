<?php

use yii\db\Migration;

/**
 * Class m211221_071734_add_internal_notes_to_maintenance
 */
class m211221_071734_add_internal_notes_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "internal_notes", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "internal_notes");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_071734_add_internal_notes_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
