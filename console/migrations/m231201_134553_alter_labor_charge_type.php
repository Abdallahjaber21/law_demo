<?php

use yii\db\Migration;

/**
 * Class m231201_134553_alter_labor_charge_type
 */
class m231201_134553_alter_labor_charge_type extends Migration
{
    public function safeUp()
    {
        $this->alterColumn("repair_request", "labor_charge", $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("repair_request", "labor_charge", $this->integer());
    }
}
