<?php

use yii\db\Migration;

/**
 * Handles adding expire_at to table `contract`.
 */
class m191119_070333_add_expire_at_column_to_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contract', 'expire_at', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('contract', 'expire_at');
    }
}
