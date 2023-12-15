<?php

use yii\db\Migration;

/**
 * Class m191028_143902_add_problem_id_to_repair_request
 */
class m191028_143902_add_problem_id_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "problem_id", $this->integer());
        $this->createIndex("fk_repair_request_problem_problem_id_idx", "repair_request", "problem_id");
        $this->addForeignKey("fk_repair_request_problem_problem_id", "repair_request", "problem_id", "problem", "id", 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_repair_request_problem_problem_id", "repair_request");
        $this->dropIndex("fk_repair_request_problem_problem_id_idx", "repair_request");
        $this->dropColumn("repair_request", "problem_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191028_143902_add_problem_id_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
