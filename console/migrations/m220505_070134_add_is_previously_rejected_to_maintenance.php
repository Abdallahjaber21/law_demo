<?php

use common\models\Maintenance;
use yii\db\Migration;

/**
 * Class m220505_070134_add_is_previously_rejected_to_maintenance
 */
class m220505_070134_add_is_previously_rejected_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Maintenance::tableName(), 'is_previously_rejected', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Maintenance::tableName(), 'is_previously_rejected');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220505_070134_add_is_previously_rejected_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
