<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%equipment_category}}`.
 */
class m230131_072330_create_equipment_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%equipment_category}}', [
            'id' => $this->primaryKey(),
            'key'  => $this->string()->notNull()->unique(),
            'name' => $this->string()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%equipment_category}}');
    }
}
