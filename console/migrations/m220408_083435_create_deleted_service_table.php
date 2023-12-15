<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%deleted_service}}`.
 */
class m220408_083435_create_deleted_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%deleted_service}}', [
            'id'         => $this->primaryKey(),
            'service_id' => $this->integer(),
            'model'      => $this->text(),
            'logs'       => $this->text(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%deleted_service}}');
    }
}
