<?php

use yii\db\Migration;

/**
 * Class m230808_103118_add_attributes_to_admin
 */
class m230808_103118_add_attributes_to_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("admin", "created_by", $this->integer());
        $this->addColumn("admin", "updated_by", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("admin", "created_by");
        $this->dropColumn("admin", "updated_by");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_103118_add_attributes_to_admin cannot be reverted.\n";

        return false;
    }
    */
}
