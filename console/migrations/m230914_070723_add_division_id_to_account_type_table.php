<?php

use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m230914_070723_add_division_id_to_account_type_table
 */
class m230914_070723_add_division_id_to_account_type_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account_type', 'label', $this->string()->after('name'));
        $this->addColumnWithForeignKey('account_type', 'division_id', $this->integer()->after('name'), 'division', 'id');
        $this->addColumn('account_type', 'for_backend', $this->boolean()->after('parent_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account_type', 'label');
        $this->dropColumnWithForeignKey('account_type', 'division_id', 'division', 'id');
        $this->dropColumn('account_type', 'for_backend');
    }
}
