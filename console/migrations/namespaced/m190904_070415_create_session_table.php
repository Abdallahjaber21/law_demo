<?php
namespace console\migrations\namespaced;

use yii\db\Migration;

/**
 * Handles the creation of table `session`.
 */
class m190904_070415_create_session_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%session}}', [
            'id' => $this->char(64)->notNull(),
            'user_id' => $this->integer(),
            'expire' => $this->integer(),
            'last_write' => $this->integer(),
            'data' => $this->binary()
        ]);
        $this->addPrimaryKey('pk-id', '{{%session}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%session}}');
    }
}
