<?php

use yii\db\Migration;

/**
 * Class m220314_091913_add_completed_by_to_repair_request
 */
class m220314_091913_add_completed_by_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "completed_by", $this->integer());
        $completedBy = \yii\helpers\ArrayHelper::map(
         \common\models\ServiceLog::find()
            ->where(['log_message'=>["Completed service", "Completed service + create new one"]])
            ->asArray()
            ->all(), "service_id", "updated_by");
        $updateList = [];
        foreach ($completedBy as $service_id => $updated_by) {
           if(empty($updateList[$updated_by])){
               $updateList[$updated_by] = [];
           }
            $updateList[$updated_by][] = $service_id;
        }
        foreach ($updateList as $completed_by => $serviceIds) {
            \common\models\RepairRequest::updateAll([
                'completed_by' => $completed_by
            ], [
                'id'=> $serviceIds
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "completed_by");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_091913_add_completed_by_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
