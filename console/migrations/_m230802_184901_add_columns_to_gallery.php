<?php

use yii\db\Migration;

/**
 * Class m230802_184901_add_columns_to_gallery
 */
class m230802_184901_add_columns_to_gallery extends Migration
{
    public function safeUp()
    {
        $this->addColumn("gallery", "repair_request_id", $this->integer());
        $this->createIndex("fk_gallery_repair_request_id_idx", "gallery", "repair_request_id");
        $this->addForeignKey(
            'fk_gallery_repair_request_id',
            'gallery',
            'repair_request_id',
            'repair_request',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("gallery", "description", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_gallery_repair_request_id", "gallery");
        $this->dropIndex("fk_gallery_repair_request_id_idx", "gallery");
        $this->dropColumn("gallery", "repair_request_id");
        $this->dropColumn("gallery", "description");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_184901_add_columns_to_gallery cannot be reverted.\n";

        return false;
    }
    */
}
