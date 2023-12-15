<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m221221_080602_add_worker_id_to_repair_service
 */
class m221221_080602_add_worker_id_to_repair_service extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), 'worker_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), 'worker_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221221_080602_add_worker_id_to_repair_service cannot be reverted.\n";

        return false;
    }
    */
}
