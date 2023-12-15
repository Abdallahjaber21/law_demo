<?php

use yii\db\Migration;

/**
 * Class m191028_132448_add_coordinates_to_location
 */
class m191028_132448_add_coordinates_to_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("location", 'address', $this->string());
        $this->addColumn("location", 'latitude', $this->string());
        $this->addColumn("location", 'longitude', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("location", 'address');
        $this->dropColumn("location", 'latitude');
        $this->dropColumn("location", 'longitude');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191028_132448_add_coordinates_to_location cannot be reverted.\n";

        return false;
    }
    */
}
