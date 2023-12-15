<?php

use yii\db\Migration;

/**
 * Handles adding eta to table `repair_request`.
 */
class m191031_145154_add_eta_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'eta', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'eta');
    }
}
