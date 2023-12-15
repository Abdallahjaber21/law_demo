<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%equipment_type}}`.
 */
class m230825_120655_update_equipment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('equipment_type', 'code', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('equipment_type', 'code', $this->integer()->notnull());
    }
}
