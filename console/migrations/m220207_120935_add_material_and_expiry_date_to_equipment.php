<?php

use yii\db\Migration;

/**
 * Class m220207_120935_add_material_and_expiry_date_to_equipment
 */
class m220207_120935_add_material_and_expiry_date_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "material", $this->string());
        $this->addColumn("equipment", "expire_at", $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "material");
        $this->dropColumn("equipment", "expire_at");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_120935_add_material_and_expiry_date_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
