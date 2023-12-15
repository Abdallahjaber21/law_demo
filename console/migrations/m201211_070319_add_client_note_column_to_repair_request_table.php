<?php

use yii\db\Migration;

/**
 * Handles adding client_note to table `repair_request`.
 */
class m201211_070319_add_client_note_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request",  "note_client", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request",  "note_client");
    }
}
