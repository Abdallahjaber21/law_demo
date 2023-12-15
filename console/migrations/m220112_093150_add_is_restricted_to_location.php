<?php

use yii\db\Migration;

/**
 * Class m220112_093150_add_is_restricted_to_location
 */
class m220112_093150_add_is_restricted_to_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("location", "is_restricted", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("location", "is_restricted");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_093150_add_is_restricted_to_location cannot be reverted.\n";

        return false;
    }
    */
}
