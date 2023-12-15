<?php

use common\models\RepairRequestChats;
use yii\db\Migration;

/**
 * Class m231120_144124_add_audio_to_repair_request_chats_table
 */
class m231120_144124_add_audio_to_repair_request_chats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(RepairRequestChats::tableName(), 'audio', $this->string()->after('message'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(RepairRequestChats::tableName(), 'audio');
    }
}
