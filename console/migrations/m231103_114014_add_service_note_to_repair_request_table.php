<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m231103_114014_add_service_note_to_repair_request_table
 */
class m231103_114014_add_service_note_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'service_note', $this->text()->after('note'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'service_note');
    }
}
