<?php

use yii\db\Migration;

/**
 * Handles adding completed_at to table `repair_request`.
 */
class m191125_114354_add_completed_at_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'completed_at', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'completed_at');
    }
}
