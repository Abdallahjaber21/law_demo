<?php

use common\models\RepairRequest;
use common\models\VillaPpmTemplates;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231115_143012_add_column_template_id_to_repair_request_table
 */
class m231115_143012_add_column_template_id_to_repair_request_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'template_id', $this->integer()->after('category_id'), VillaPpmTemplates::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'template_id', VillaPpmTemplates::tableName(), 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231115_143012_add_column_template_id_to_repair_request_table cannot be reverted.\n";

        return false;
    }
    */
}
