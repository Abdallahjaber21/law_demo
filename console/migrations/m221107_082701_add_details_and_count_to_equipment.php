<?php

use yii\db\Migration;

/**
 * Class m221107_082701_add_details_and_count_to_equipment
 */
class m221107_082701_add_details_and_count_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "details", $this->string());
        $this->addColumn("equipment", "quantity", $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "details");
        $this->dropColumn("equipment", "quantity");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_082701_add_details_and_count_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
