<?php

use yii\db\Migration;

/**
 * Class m230818_082404_add_segment_path_id_to_location_table
 */
class m230818_082404_add_segment_path_id_to_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("location", "segment_path_id", $this->integer()->after('customer_id'));
        $this->createIndex("fk_location_segment_path_id_idx", "location", "segment_path_id");
        $this->addForeignKey(
            'fk_location_segment_path_id',
            'location',
            'segment_path_id',
            'segment_path',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_location_segment_path_id', 'location');
        $this->dropIndex('fk_location_segment_path_id_idx', 'location');
        $this->dropColumn('location', 'segment_path_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230818_082404_add_segment_path_id_to_location_table cannot be reverted.\n";

        return false;
    }
    */
}
