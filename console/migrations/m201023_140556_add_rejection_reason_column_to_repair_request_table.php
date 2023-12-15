<?php

use yii\db\Migration;

/**
 * Handles adding rejection_reason to table `repair_request`.
 */
class m201023_140556_add_rejection_reason_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'rejection_reason', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'rejection_reason');
    }
}
