<?php

use common\models\Assignee;
use common\models\Gallery;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%repair_request_chats}}`.
 */
class m231117_132924_create_repair_request_chats_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "repair_request_chats";
    }

    public function columns()
    {
        return [
            'request_id' => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            'assignee_id' => $this->foreignKey(Assignee::tableName(), 'user_id', $this->integer()),
            'gallery_id' => $this->foreignKey(Gallery::tableName(), 'id', $this->integer()),
            'message' => $this->text()
        ];
    }
}
