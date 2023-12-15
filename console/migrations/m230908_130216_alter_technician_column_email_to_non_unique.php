<?php

use yii\db\Migration;

/**
 * Class m230908_130216_alter_technician_column_email_to_non_unique
 */
class m230908_130216_alter_technician_column_email_to_non_unique extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('email', 'technician');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('email', 'technician', 'email', true);
    }
}
