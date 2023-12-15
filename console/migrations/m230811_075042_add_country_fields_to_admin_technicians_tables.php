<?php

use yii\db\Migration;

/**
 * Class m230811_075042_add_country_fields_to_admin_technicians_tables
 */
class m230811_075042_add_country_fields_to_admin_technicians_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('admin', 'country', $this->string()->after('email'));
        $this->addColumn('technician', 'country', $this->string()->after('email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('admin', 'country');
        $this->dropColumn('technician', 'country');
    }
}
