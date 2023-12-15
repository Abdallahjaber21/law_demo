<?php

use yii\db\Migration;

/**
 * Class m210510_122224_add_code_to_tehcnician
 */
class m210510_122224_add_code_to_tehcnician extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("technician", "code", $this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("technician", "code");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210510_122224_add_code_to_tehcnician cannot be reverted.\n";

        return false;
    }
    */
}
