<?php

use yii\db\Migration;

/**
 * Class m220330_052711_add_is_locked_to_user_location
 */
class m220330_052711_add_is_locked_to_user_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user_location","is_locked", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user_location", "is_locked");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_052711_add_is_locked_to_user_location cannot be reverted.\n";

        return false;
    }
    */
}
