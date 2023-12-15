<?php

use yii\db\Migration;

/**
 * Class m230906_070317_change_type_from_admin_table
 */
class m230906_070317_change_type_from_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("admin", "email", $this->string()->unique());

        $this->alterColumn("admin", "phone_number", $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("admin", "email", $this->string());
        $this->alterColumn("admin", "phone_number", $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230906_070317_change_type_from_admin_table cannot be reverted.\n";

        return false;
    }
    */
}
