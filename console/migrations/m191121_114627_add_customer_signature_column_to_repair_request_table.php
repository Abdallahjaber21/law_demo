<?php

use yii\db\Migration;

/**
 * Handles adding customer_signature to table `repair_request`.
 */
class m191121_114627_add_customer_signature_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'customer_signature', $this->string());
        $this->addColumn('repair_request', 'random_token', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'random_token');
        $this->dropColumn('repair_request', 'customer_signature');
    }
}
