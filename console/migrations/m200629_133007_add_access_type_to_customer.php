<?php

use yii\db\Migration;

/**
 * Class m200629_133007_add_access_type_to_customer
 */
class m200629_133007_add_access_type_to_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "access_type", $this->integer()->defaultValue(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user", "access_type");
    }

}
