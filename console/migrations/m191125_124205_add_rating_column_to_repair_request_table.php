<?php

use yii\db\Migration;

/**
 * Handles adding rating to table `repair_request`.
 */
class m191125_124205_add_rating_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'rating', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'rating');
    }
}
