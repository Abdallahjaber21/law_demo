<?php
namespace console\migrations\namespaced;

use yii\db\Migration;

/**
 * Class m191113_083051_add_title_to_notification
 */
class m191113_083051_add_title_to_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("notification", "title", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("notification", "title");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191113_083051_add_title_to_notification cannot be reverted.\n";

        return false;
    }
    */
}
