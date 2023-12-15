<?php

use yii\db\Migration;

/**
 * Handles adding type to table `equipment`.
 */
class m200618_124039_add_type_column_to_equipment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('equipment', 'type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('equipment', 'type');
    }
}
