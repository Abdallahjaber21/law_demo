<?php

use yii\db\Migration;

/**
 * Class m230823_114557_add_has_shifts_to_division_table
 */
class m230823_114557_add_has_shifts_to_division_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('division', 'has_shifts', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('division', 'has_shifts');
    }
}
