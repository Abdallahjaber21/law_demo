<?php

use yii\db\Migration;

/**
 * Class m230802_183104_add_columns_to_repair_request
 */
class m230802_183104_add_columns_to_repair_request extends Migration
{
    public function safeUp()
    {
        $this->addColumn("repair_request", "owner_id", $this->integer());
        $this->createIndex("fk_repair_request_owner_id_idx", "repair_request", "owner_id");
        $this->addForeignKey(
            'fk_repair_request_owner_id',
            'repair_request',
            'owner_id',
            'technician',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("repair_request", "team_leader_id", $this->integer());
        $this->createIndex("fk_repair_request_team_leader_id_idx", "repair_request", "team_leader_id");
        $this->addForeignKey(
            'fk_repair_request_team_leader_id',
            'repair_request',
            'team_leader_id',
            'technician',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("repair_request", "description", $this->text());
        $this->addColumn("repair_request", "urgent_status", $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_repair_request_owner_id", "repair_request");
        $this->dropIndex("fk_repair_request_owner_id_idx", "repair_request");
        $this->dropColumn("repair_request", "owner_id");
        $this->dropForeignKey("fk_repair_request_team_leader_id", "repair_request");
        $this->dropIndex("fk_repair_request_team_leader_id_idx", "repair_request");
        $this->dropColumn("repair_request", "team_leader_id");
        $this->dropColumn("repair_request", "description");
        $this->dropColumn("repair_request", "urgent_status");
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_183104_add_columns_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
