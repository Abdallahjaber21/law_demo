<?php

use common\models\Admin;
use yii\db\Migration;

/**
 * Class m231213_075229_add_signature_to_admin
 * 
 */
class m231213_075229_add_signature_to_admin extends Migration
{
    public function safeUp()
    {
        $this->addColumn(Admin::tableName(), 'signature', $this->string()->after('image'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Admin::tableName(), 'signature');
    }
}
