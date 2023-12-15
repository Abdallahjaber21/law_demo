<?php

use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231214_125935_add_admin_signature_to_request_table
 */
class m231214_125935_add_admin_signature_to_request_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'admin_signature', $this->string());
        $this->addColumn(RepairRequest::tableName(), 'coordinator_note', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'admin_signature');
        $this->dropColumn(RepairRequest::tableName(), 'coordinator_note');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231214_125935_add_admin_signature_to_request_table cannot be reverted.\n";

        return false;
    }
    */
}
