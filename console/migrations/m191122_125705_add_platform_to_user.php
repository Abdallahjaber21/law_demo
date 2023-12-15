<?php

use yii\db\Migration;

/**
 * Class m191122_125705_add_platform_to_user
 */
class m191122_125705_add_platform_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "platform", $this->string());
        $this->addColumn("technician", "platform", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user", "platform");
        $this->dropColumn("technician", "platform");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191122_125705_add_platform_to_user cannot be reverted.\n";

        return false;
    }
    */
}
