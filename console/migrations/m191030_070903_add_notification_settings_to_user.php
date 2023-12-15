<?php

use yii\db\Migration;

/**
 * Class m191030_070903_add_notification_settings_to_user
 */
class m191030_070903_add_notification_settings_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "contracts_reminders", $this->boolean()->defaultValue(true));
        $this->addColumn("user", "maintenance_notifications", $this->boolean()->defaultValue(true));
        $this->addColumn("user", "news_notifications", $this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user", "contracts_reminders");
        $this->dropColumn("user", "maintenance_notifications");
        $this->dropColumn("user", "news_notifications");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191030_070903_add_notification_settings_to_user cannot be reverted.\n";

        return false;
    }
    */
}
