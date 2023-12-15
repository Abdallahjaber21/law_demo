<?php

use common\models\Category;
use common\models\Location;
use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231103_125933_fix_bug_category_id
 */
class m231103_125933_fix_bug_category_id extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'category_id', $this->integer()->after('equipment_id'), Category::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'category_id', Category::tableName(), 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231103_125933_fix_bug_category_id cannot be reverted.\n";

        return false;
    }
    */
}
