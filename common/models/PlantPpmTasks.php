<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "plant_ppm_tasks".
 *
 * @property integer $id
 * @property string $name
 * @property integer $task_type
 * @property integer $occurence_value
 * @property integer $meter_type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property string $status_label
 * @property label $status_list
 */
class PlantPpmTasks extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    const TASK_TYPE_CHECKLIST = 10;
    const TASK_TYPE_SERVICE = 20;

    const METER_TYPE_KM = 10;
    const METER_TYPE_HOUR = 20;
    const METER_TYPE_DAY = 30;
    const METER_TYPE_WEEK = 40;
    const METER_TYPE_MONTH = 50;
    const METER_TYPE_YEAR = 60;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plant_ppm_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_type', 'occurence_value', 'meter_type', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name', 'task_type', 'occurence_value', 'meter_type'], 'required']
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression("now()"),
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            'task_type' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'task_type',
                'options' => [
                    self::TASK_TYPE_CHECKLIST => Yii::t("app", "Checklist"),
                    self::TASK_TYPE_SERVICE => Yii::t("app", "Service"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            'meter_type' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'meter_type',
                'options' => [
                    self::METER_TYPE_KM => Yii::t("app", "Km"),
                    self::METER_TYPE_HOUR => Yii::t("app", "Hour"),
                    self::METER_TYPE_DAY => Yii::t("app", "Day"),
                    self::METER_TYPE_WEEK => Yii::t("app", "Week"),
                    self::METER_TYPE_MONTH => Yii::t("app", "Month"),
                    self::METER_TYPE_YEAR => Yii::t("app", "Year"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            // 'multilingual' => [
            // 'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            // 'attributes' => []
            // ],
        ];
    }
    // use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    // public static function find()
    // {
    // return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    // }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'task_type' => 'Task Type',
            'occurence_value' => 'Occurence Value',
            'meter_type' => 'Meter Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */

    /*
public function beforeDelete() {
if (parent::beforeDelete()) {
if ($this->status == self::STATUS_ENABLED) {
$this->status = self::STATUS_DISABLED;
$this->save();
return false;
} else {
return true;
}
}
return false;
}
*/
}
