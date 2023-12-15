<?php
namespace console\migrations\namespaced;

use yii\db\Migration;

/**
 * Handles the creation of table `account`.
 */
class m180104_102728_create_account_table extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'type' => $this->string()
                ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('account');
    }

}
