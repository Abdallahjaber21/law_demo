<?php

use yii\db\Migration;

/**
 * Handles adding assigned_at to table `repair_request`.
 */
class m191031_082040_add_assigned_at_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'assigned_at', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'assigned_at');
    }
}
