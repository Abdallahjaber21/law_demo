<?php

use yii\db\Migration;

/**
 * Handles adding type to table `problem`.
 */
class  m201023_053633_add_type_column_to_problem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('problem', 'type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('problem', 'type');
    }
}
