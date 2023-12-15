<?php

use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%repair_request}}`.
 */
class m231027_060817_add_columns_to_repair_request_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'supervisor_note', $this->string());
        $this->addColumn(RepairRequest::tableName(), 'supervisor_signature', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'supervisor_note');
        $this->dropColumn(RepairRequest::tableName(), 'supervisor_signature');
    }
}
