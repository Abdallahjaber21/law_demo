<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m231127_085823_add_labor_charge_hour_to_repair_request_table
 */
class m231127_085823_add_labor_charge_hour_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'labor_charge', $this->integer()->after('service_type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'labor_charge');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_085823_add_labor_charge_hour_to_repair_request_table cannot be reverted.\n";

        return false;
    }
    */
}
