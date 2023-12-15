<?php

use yii\db\Migration;

class m230807_112733_add_type_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("account", "type", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("account", "type", $this->string());
    }
}
