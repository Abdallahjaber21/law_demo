<?php

use yii\db\Migration;

/**
 * Class m230802_170414_add_columns_to_account
 */
class m230802_170414_add_columns_to_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("account", "for_backend", $this->integer()->defaultValue(0));
        $this->addColumn("account", "description", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("account", "for_backend");
        $this->dropColumn("account", "description");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_170414_add_columns_to_account cannot be reverted.\n";

        return false;
    }
    */
}
