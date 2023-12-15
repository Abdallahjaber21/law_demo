<?php

use yii\db\Migration;

/**
 * Class m230816_071232_add_new_equipment_table_columns
 */
class m230816_071232_add_new_equipment_table_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('equipment', 'columnName', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('equipment', 'columnName');
    }
}
