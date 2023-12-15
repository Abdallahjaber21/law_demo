<?php

use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m220113_063354_add_hard_copy_image_to_repair_request
 */
class m220113_063354_add_hard_copy_image_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequest::tableName(), "hard_copy_report", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequest::tableName(), "hard_copy_report");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220113_063354_add_hard_copy_image_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
