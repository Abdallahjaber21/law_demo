<?php

use yii\db\Migration;

/**
 * Handles adding problem_input to table `repair_request`.
 */
class m201023_062747_add_problem_input_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'problem_input', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('repair_request', 'problem_input');
    }
}
