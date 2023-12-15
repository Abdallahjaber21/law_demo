<?php

use common\models\Location;
use yii\db\Migration;

/**
 * Class m231127_102900_add_expiry_date_to_location_table
 */
class m231127_102900_add_expiry_date_to_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Location::tableName(), 'expiry_date', $this->dateTime()->after('updated_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Location::tableName(), 'expiry_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_102900_add_expiry_date_to_location_table cannot be reverted.\n";

        return false;
    }
    */
}
