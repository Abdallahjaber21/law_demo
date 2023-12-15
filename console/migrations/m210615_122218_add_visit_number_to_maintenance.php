<?php

use yii\db\Migration;

/**
 * Class m210615_122218_add_visit_number_to_maintenance
 */
class m210615_122218_add_visit_number_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "visit_number", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "visit_number");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_122218_add_visit_number_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
