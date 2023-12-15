<?php

use common\models\Assignee;
use yii\db\Migration;

/**
 * Class m231010_121339_add_datetime_to_assignee_table
 */
class m231010_121339_add_datetime_to_assignee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Assignee::tableName(), 'datetime', $this->dateTime()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Assignee::tableName(), 'datetime');
    }
}
