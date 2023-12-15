<?php

use common\models\Image;
use yii\db\Migration;

/**
 * Class m231025_131800_add_note_field_to_image_table
 */
class m231025_131800_add_note_field_to_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Image::tableName(), 'note', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Image::tableName(), 'note');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231025_131800_add_note_field_to_image_table cannot be reverted.\n";

        return false;
    }
    */
}
