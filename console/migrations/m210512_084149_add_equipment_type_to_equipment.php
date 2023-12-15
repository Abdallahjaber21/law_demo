<?php

use yii\db\Migration;

/**
 * Class m210512_084149_add_equipment_type_to_equipment
 */
class m210512_084149_add_equipment_type_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "equipment_type", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "equipment_type");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210512_084149_add_equipment_type_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
