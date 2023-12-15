<?php

use yii\db\Migration;

/**
 * Class m220211_070629_add_gallery_id_to_maintenance
 */
class m220211_070629_add_gallery_id_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "gallery_id", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "gallery_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220211_070629_add_gallery_id_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
