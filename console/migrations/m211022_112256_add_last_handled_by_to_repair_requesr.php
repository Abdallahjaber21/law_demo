<?php

use yii\db\Migration;

/**
 * Class m211022_112256_add_last_handled_by_to_repair_requesr
 */
class m211022_112256_add_last_handled_by_to_repair_requesr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("repair_request", "last_handled_by", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("repair_request", "last_handled_by");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_112256_add_last_handled_by_to_repair_requesr cannot be reverted.\n";

        return false;
    }
    */
}
