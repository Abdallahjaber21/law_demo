<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%repair_request}}`.
 */
class m211022_083432_add_pending_equipment_id_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "pending_equipment_id", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "pending_equipment_id");
    }
}
