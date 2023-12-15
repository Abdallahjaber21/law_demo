<?php

use yii\db\Migration;

/**
 * Class m191028_113944_add_first_last_names_to_user
 */
class m191028_113944_add_first_last_names_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "firstname", $this->string());
        $this->addColumn("user", "lastname", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user", "firstname");
        $this->dropColumn("user", "lastname");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191028_113944_add_first_last_names_to_user cannot be reverted.\n";

        return false;
    }
    */
}
