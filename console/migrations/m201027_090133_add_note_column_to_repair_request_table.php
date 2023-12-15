<?php

use yii\db\Migration;

/**
 * Handles adding note to table `repair_request`.
 */
class m201027_090133_add_note_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'note', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'note');
    }
}
