<?php

use yii\db\Migration;

/**
 * Class m211208_062632_add_temp_in_out_to_equipment
 */
class m211208_062632_add_temp_in_out_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "temporary_in", $this->boolean()->defaultValue(false));
        $this->addColumn("equipment", "temporary_out", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "temporary_in");
        $this->dropColumn("equipment", "temporary_out");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211208_062632_add_temp_in_out_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
