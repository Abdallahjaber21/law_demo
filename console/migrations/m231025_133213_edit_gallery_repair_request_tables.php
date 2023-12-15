<?php

use common\models\Gallery;
use common\models\RepairRequest;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231025_133213_edit_gallery_repair_request_tables
 */
class m231025_133213_edit_gallery_repair_request_tables extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey(RepairRequest::tableName(), 'gallery_id', $this->integer()->after('team_leader_id'), Gallery::tableName(), 'id');
        // $this->dropColumnWithForeignKey(Gallery::tableName(), 'repair_request_id',  RepairRequest::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey(RepairRequest::tableName(), 'gallery_id',  Gallery::tableName(), 'id');
        // $this->addColumnWithForeignKey(Gallery::tableName(), 'repair_request_id', $this->integer(), RepairRequest::tableName(), 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231025_133213_edit_gallery_repair_request_tables cannot be reverted.\n";

        return false;
    }
    */
}
