<?php

use common\models\RepairRequest;
use common\models\Sector;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231106_132750_add_sector_id_to_repair_request_table
 */
class m231106_132750_add_sector_id_to_repair_request_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey(RepairRequest::tableName(), "sector_id", $this->integer()->after("equipment_id"), Sector::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), "sector_id", Sector::tableName(), 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231106_132750_add_sector_id_to_repair_request_table cannot be reverted.\n";

        return false;
    }
    */
}
