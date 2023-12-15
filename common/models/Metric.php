<?php

namespace common\models;

use DateTime;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "metric".
 *
 * @property integer $id
 * @property string $owner_type
 * @property integer $owner_id
 * @property string $key
 * @property string $date
 * @property double $value
 *
 */
class Metric extends ActiveRecord {

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return 'metric';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
        [['owner_id'], 'integer'],
        [['date'], 'safe'],
        [['value'], 'number'],
        [['owner_type', 'key'], 'string', 'max' => 255],
    ];
  }

  public static function findEnabled() {
    return parent::find()->where(['status' => self::STATUS_ENABLED]);
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
        'id' => Yii::t('app', 'ID'),
        'owner_type' => Yii::t('app', 'Owner Type'),
        'owner_id' => Yii::t('app', 'Owner ID'),
        'key' => Yii::t('app', 'Key'),
        'date' => Yii::t('app', 'Date'),
        'value' => Yii::t('app', 'Value'),
    ];
  }

  public static function addTo($type, $owner, $key, $value, $date = null) {
    if (empty($date)) {
      $date = date("Y-m-d");
    }
    $metric = Metric::find()
            ->where([
                'AND',
                ['owner_type' => $type],
                ['owner_id' => $owner],
                ['key' => $key],
                ['date' => $date],
            ])
            ->one();
    if (empty($metric)) {
      $metric = new Metric();
      $metric->owner_type = $type;
      $metric->owner_id = $owner;
      $metric->key = $key;
      $metric->date = $date;
      $metric->value = $value;
      $metric->save();
    } else {
      $metric->value += $value;
      $metric->save();
    }
  }

  public static function getSumInRange($type, $owner, $key, $from, $to) {
    return Metric::find()
                    ->filterWhere([
                        'AND',
                        ['owner_type' => $type],
                        ['owner_id' => $owner],
                        ['key' => $key],
                        ['>=', 'date', $from],
                        ['<=', 'date', $to],
                    ])
                    ->sum("value") + 0.0;
  }

  public static function getListInRange($type, $owner, $key, $from, $to) {
    $start = new DateTime($from);
    $end = new DateTime($to);
    $diff = $end->diff($start)->format("%a");

    $result = [];
    $values = Metric::find()
            ->select(['sum(value) as sum', "date"])
            ->filterWhere([
                'AND',
                ['owner_type' => $type],
                ['owner_id' => $owner],
                ['key' => $key],
                ['>=', 'date', $from],
                ['<=', 'date', $to],
            ])
            ->groupBy(["date"])
            ->indexBy("date")
            ->asArray()
            ->all();
    for ($index = 0; $index <= $diff; $index++) {
      $date = date("Y-m-d", strtotime("{$from} + $index days"));
      $value = !empty($values[$date]) ? $values[$date]['sum'] : 0;
      $result[] = [
          'date' => $date,
          'label' => date("M j", strtotime($date)),
          'value' => $value,
      ];
    }

    return $result;
  }

  public static function getMultiSumInRange($type, $owners, $key, $from, $to) {
    $values = Metric::find()
            ->select(['sum(value) as sum', 'owner_id'])
            ->filterWhere([
                'AND',
                ['owner_type' => $type],
                ['owner_id' => $owners],
                ['key' => $key],
                ['>=', 'date', $from],
                ['<=', 'date', $to],
            ])
            ->groupBy(["owner_id"])
            ->indexBy("owner_id")
            ->asArray()
            ->all();
    
    return $values;
  }

}
