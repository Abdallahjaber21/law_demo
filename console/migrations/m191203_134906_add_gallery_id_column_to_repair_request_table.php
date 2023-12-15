<?php

use yii\db\Migration;

/**
 * Handles adding gallery_id to table `repair_request`.
 */
class m191203_134906_add_gallery_id_column_to_repair_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('repair_request', 'gallery_id', $this->integer());

        $this->createIndex("fk_repair_request_gallery_idx", "repair_request", "gallery_id");
        $this->addForeignKey("fk_repair_request_gallery", "repair_request", "gallery_id", "gallery", "id", "SET NULL", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_repair_request_gallery", "repair_request");
        $this->dropIndex("fk_repair_request_gallery_idx", "repair_request");
        $this->dropColumn('repair_request', 'gallery_id');
    }
}
