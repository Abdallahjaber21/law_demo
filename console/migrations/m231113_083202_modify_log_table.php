<?php

use common\models\Gallery;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231113_083202_modify_log_table
 */
class m231113_083202_modify_log_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('log', 'date_time');
        $this->alterColumn('log', 'type', $this->integer());
        $this->renameColumn('log', 'note', 'title');
        $this->dropColumnWithForeignKey('log', 'gallery_id', Gallery::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('log', 'date_time', $this->dateTime());
        $this->alterColumn('log', 'type', $this->string(255));
        $this->renameColumn('log', 'title', 'note');
        $this->addColumnWithForeignKey('log', 'gallery_id', $this->integer(), Gallery::tableName(), 'id');
    }
}
