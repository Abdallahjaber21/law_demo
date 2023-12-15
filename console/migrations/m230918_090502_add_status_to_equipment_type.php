<?php

use yii\db\Migration;

/**
 * Class m230918_090502_add_status_to_equipment_type
 */
class m230918_090502_add_status_to_equipment_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('equipment_type', 'status', $this->integer(11)->defaultValue(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('equipment_type', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_090502_add_status_to_equipment_type cannot be reverted.\n";

        return false;
    }
    */
}
