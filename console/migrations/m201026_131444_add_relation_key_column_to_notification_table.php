<?php

use yii\db\Migration;

/**
 * Handles adding relation_key to table `notification`.
 */
class m201026_131444_add_relation_key_column_to_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notification', 'relation_key', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('notification', 'relation_key');
    }
}
