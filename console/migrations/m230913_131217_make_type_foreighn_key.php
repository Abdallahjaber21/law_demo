<?php

use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m230913_131217_make_type_foreighn_key
 */
class m230913_131217_make_type_foreighn_key extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('account', 'type');
        $this->addColumnWithForeignKey('account', 'type', $this->integer()->after('id'), 'account_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey('account', 'type', 'account_type', 'id');
        $this->addColumn('account', 'type', $this->integer());
    }
}
