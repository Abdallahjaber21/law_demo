<?php

use yii\db\Migration;

/**
 * Class m230919_060741_add_code_to_segment_path_table
 */
class m230919_060741_add_code_to_segment_path_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('segment_path', 'code', $this->string(255)->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('segment_path', 'code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_060741_add_code_to_segment_path_table cannot be reverted.\n";

        return false;
    }
    */
}
