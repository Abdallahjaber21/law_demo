<?php

use yii\db\Migration;

/**
 * Class m231018_083922_add_remarks_to_plant_ppm_history
 */
class m231018_083922_add_remarks_to_plant_ppm_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('plant_ppm_tasks_history', 'remarks', $this->string());
        $this->addColumn('mall_ppm_tasks_history', 'remarks', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('plant_ppm_tasks_history', 'remarks');
        $this->dropColumn('mall_ppm_tasks_history', 'remarks');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231018_083922_add_remarks_to_plant_ppm_history cannot be reverted.\n";

        return false;
    }
    */
}
