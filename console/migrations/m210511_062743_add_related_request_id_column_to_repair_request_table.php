<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%repair_request}}`.
 */
class m210511_062743_add_related_request_id_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "related_request_id", $this->integer());
        $this->createIndex("idx_repair_request_related_request_id", "repair_request", "related_request_id");
        $this->addForeignKey("fk_repair_request_related_request_id", "repair_request", "related_request_id", "repair_request", "id", "SET NULL", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_repair_request_related_request_id", "repair_request");
        $this->dropIndex("idx_repair_request_related_request_id", "repair_request");
        $this->dropColumn("repair_request", "related_request_id");
    }
}
