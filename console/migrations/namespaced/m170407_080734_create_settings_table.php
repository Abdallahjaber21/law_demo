<?php
namespace console\migrations\namespaced;

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m170407_080734_create_settings_table extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('setting_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'label' => $this->string()->notNull(),
            'description' => $this->string(),
                ], $tableOptions);

        $this->createTable('setting', [
            'id' => $this->primaryKey(),
            'setting_category_id' => $this->integer(),
            'name' => $this->string()->unique()->notNull(),
            'label' => $this->string()->notNull(),
            'description' => $this->string(),
            'type' => $this->integer()->notNull(),
            'value' => $this->text(),
            'config' => $this->text(),
                ], $tableOptions);

        $this->createIndex("fk_setting_setting_category_idx", "setting", "setting_category_id");
        $this->addForeignKey("fk_setting_setting_category", "setting", "setting_category_id", "setting_category", "id", "CASCADE", "CASCADE");
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropForeignKey("fk_setting_setting_category", "setting");
        $this->dropIndex("fk_setting_setting_category_idx", "setting");
        $this->dropTable('setting');
        $this->dropTable('setting_category');
    }

}
