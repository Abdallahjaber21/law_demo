<?php

use yii\db\Migration;


class m230920_094704_add_code_to_category_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('category', 'code', $this->string()->after('name'));
    }

    public function safeDown()
    {
        $this->dropColumn('category', 'code');
    }
}
