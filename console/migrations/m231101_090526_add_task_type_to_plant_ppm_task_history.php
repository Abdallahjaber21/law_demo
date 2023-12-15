<?php

use common\models\PlantPpmTasksHistory;
use yii\db\Migration;

/**
 * Class m231101_090526_add_task_type_to_plant_ppm_task_history
 */
class m231101_090526_add_task_type_to_plant_ppm_task_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(PlantPpmTasksHistory::tableName(), 'task_type', $this->integer()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(PlantPpmTasksHistory::tableName(), 'task_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_090526_add_task_type_to_plant_ppm_task_history cannot be reverted.\n";

        return false;
    }
    */
}
