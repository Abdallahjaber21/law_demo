<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m231127_144041_add_coordinator_signature_to_repair_request_table
 */
class m231127_144041_add_coordinator_signature_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'coordinator_signature', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'coordinator_signature');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_144041_add_coordinator_signature_to_repair_request_table cannot be reverted.\n";

        return false;
    }
    */
}
