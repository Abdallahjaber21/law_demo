<?php

use common\models\Maintenance;
use yii\db\Migration;

/**
 * Class m220112_113513_add_hard_copy_image_to_maintenance
 */
class m220112_113513_add_hard_copy_image_to_maintenance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Maintenance::tableName(), "hard_copy_report", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Maintenance::tableName(), "hard_copy_report");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_113513_add_hard_copy_image_to_maintenance cannot be reverted.\n";

        return false;
    }
    */
}
