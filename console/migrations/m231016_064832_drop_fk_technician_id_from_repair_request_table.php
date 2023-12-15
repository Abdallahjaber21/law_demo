<?php

use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Handles the dropping of table `{{%fk_technician_id_from_repair_request}}`.
 */
class m231016_064832_drop_fk_technician_id_from_repair_request_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'technician_id', 'technician', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'technician_id', $this->integer(), 'technician', 'id');
    }
}
