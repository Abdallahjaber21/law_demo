<?php
namespace console\migrations\namespaced;

use yii\db\Migration;

/**
 * Handles the creation of table `metric`.
 */
class m190311_111552_create_metric_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $this->createTable('metric', [
        'id' => $this->primaryKey(),
        'owner_type' => $this->string(),
        'owner_id' => $this->integer(),
        'key' => $this->string(),
        'date' => $this->date(),
        'value' => $this->double(),
    ]);

    $this->createIndex("idx_metric_index", "metric", ['owner_id', "key", "date"]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropIndex("idx_metric_index", "metric");
    $this->dropTable('metric');
  }

}
