<?php

use yii\db\Migration;

/**
 * Class m210617_060335_add_new_attributes_to_user
 */
class m210617_060335_add_new_attributes_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "floor_number", $this->string());
        $this->addColumn("user", "birthdate", $this->date());
        $this->addColumn("user", "job_category", $this->string());
        $this->addColumn("user", "company_name", $this->string());
        $this->addColumn("user", "job_title", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user", "floor_number");
        $this->dropColumn("user", "birthdate");
        $this->dropColumn("user", "job_category");
        $this->dropColumn("user", "company_name");
        $this->dropColumn("user", "job_title");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210617_060335_add_new_attributes_to_user cannot be reverted.\n";

        return false;
    }
    */
}
