<?php

use yii\db\Migration;

/**
 * Class m210706_083816_make_repair_rating_float
 */
class m210706_083816_make_repair_rating_float extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("repair_request", "rating", $this->double(1));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("repair_request", "rating", $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210706_083816_make_repair_rating_float cannot be reverted.\n";

        return false;
    }
    */
}
