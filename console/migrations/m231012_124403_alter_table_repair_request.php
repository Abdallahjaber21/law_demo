<?php

use common\models\LocationEquipments;
use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231012_124403_alter_table_repair_request
 */
class m231012_124403_alter_table_repair_request extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'equipment_id', 'equipment', 'id');
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'equipment_id', $this->integer()->after('technician_id'), LocationEquipments::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'equipment_id', LocationEquipments::tableName(), 'id');
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'equipment_id', $this->integer()->after('technician_id'), 'equipment', 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231012_124403_alter_table_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
