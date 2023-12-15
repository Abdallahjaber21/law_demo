<?php

use yii\db\Migration;

/**
 * Class m230815_092454_change_owner_phone_from_int_to_string
 */
class m230815_092454_change_owner_phone_from_int_to_string extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('location', 'owner_phone', $this->string(100)); //timestamp new_data_type
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('location', 'owner_phone', $this->integer()); //int is old_data_type

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_092454_change_owner_phone_from_int_to_string cannot be reverted.\n";

        return false;
    }
    */
}
