<?php

use common\models\Maintenance;
use yii\db\Migration;

/**
 * Class m211222_073812_add_complete_method_to_maintenance
 */
class m211222_073812_add_complete_method_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Maintenance::tableName(), "complete_method", $this->integer());
        $this->addColumn(Maintenance::tableName(), "completed_by_atl", $this->integer());
        $this->addColumn(Maintenance::tableName(), "duration", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Maintenance::tableName(), "duration");
        $this->dropColumn(Maintenance::tableName(), "completed_by_atl");
        $this->dropColumn(Maintenance::tableName(), "complete_method");

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_073812_add_complete_method_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
