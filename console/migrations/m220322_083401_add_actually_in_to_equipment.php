<?php

use yii\db\Migration;

/**
 * Class m220322_083401_add_actually_in_to_equipment
 */
class m220322_083401_add_actually_in_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "actually_in", $this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "actually_in");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220322_083401_add_actually_in_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
