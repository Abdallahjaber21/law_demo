<?php

use yii\db\Migration;

/**
 * Class m230911_052119_add_remarks_to_location_equipments_table
 */
class m230911_052119_add_remarks_to_location_equipments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location_equipments', 'remarks', $this->string()->after('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('location_equipments', 'remarks');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230911_052119_add_remarks_to_location_equipments_table cannot be reverted.\n";

        return false;
    }
    */
}
