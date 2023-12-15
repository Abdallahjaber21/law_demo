<?php

use yii\db\Migration;

/**
 * Class m220208_063608_make_log_message_text
 */
class m220208_063608_make_log_message_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(\common\models\ServiceLog::tableName(), 'log_message', $this->text());
        $this->alterColumn(\common\models\MaintenanceLog::tableName(), 'log_message', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220208_063608_make_log_message_text cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220208_063608_make_log_message_text cannot be reverted.\n";

        return false;
    }
    */
}
