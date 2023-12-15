<?php

use yii\db\Migration;

/**
 * Class m210512_083917_add_manufacturer_to_equipment
 */
class m210512_083917_add_manufacturer_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "manufacturer", $this->integer()->defaultValue(10));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "manufacturer");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210512_083917_add_manufacturer_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
