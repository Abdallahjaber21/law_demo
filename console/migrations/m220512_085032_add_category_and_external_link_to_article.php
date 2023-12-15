<?php

use common\models\Article;
use yii\db\Migration;

/**
 * Class m220512_085032_add_category_and_external_link_to_article
 */
class m220512_085032_add_category_and_external_link_to_article extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Article::tableName(), "category", $this->string());
        $this->addColumn(Article::tableName(), "external_link", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Article::tableName(), "category");
        $this->dropColumn(Article::tableName(), "external_link");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_085032_add_category_and_external_link_to_article cannot be reverted.\n";

        return false;
    }
    */
}
