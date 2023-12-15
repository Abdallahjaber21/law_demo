<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m211221_084616_add_notification_id_to_repair_request
 */
class m211221_084616_add_notification_id_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), "notification_id", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), "notification_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_084616_add_notification_id_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
