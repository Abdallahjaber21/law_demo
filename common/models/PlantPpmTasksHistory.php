<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "plant_ppm_tasks_history".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $meter_ratio
 * @property integer $asset_id
 * @property integer $ppm_service_id
 * @property integer $status
 * @property string $created_at
 * @property integer $task_type
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $remarks
 *
 * @property LocationEquipments $asset
 * @property RepairRequest $ppmService
 * @property PlantPpmTasks $task
 *
 * @property string $status_label
 * @property label $status_list
 */
class PlantPpmTasksHistory extends \yii\db\ActiveRecord
{

    public $task_status;

    // Status
    const STATUS_PENDING = 10;
    //const STATUS_DELETED = 30;

    const TASK_TYPE_CHECKLIST = 10;
    const TASK_TYPE_SERVICE = 20;

    const TASK_STATUS_PENDING = 10;
    const TASK_STATUS_REPLACE = 20;
    const TASK_STATUS_CHECK = 30;
    const TASK_STATUS_DONE = 40;
    const TASK_STATUS_NOT_DONE = 50;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plant_ppm_tasks_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'meter_ratio', 'asset_id', 'ppm_service_id', 'status', 'created_by', 'updated_by', 'task_type'], 'integer'],
            [['created_at', 'updated_at', 'task_status'], 'safe'],
            [['remarks'], 'string', 'max' => 255],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocationEquipments::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['ppm_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['ppm_service_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => PlantPpmTasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
                    self::STATUS_PENDING => Yii::t("app", "Pending"),
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
            'task_status' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'task_status',
                'options' => [
                    self::TASK_STATUS_PENDING => Yii::t("app", "Pending"),
                    self::TASK_STATUS_CHECK => Yii::t("app", "Check"),
                    self::TASK_STATUS_REPLACE => Yii::t("app", "Replace"),
                    self::TASK_STATUS_DONE => Yii::t("app", "YES"),
                    self::TASK_STATUS_NOT_DONE => Yii::t("app", "NO"),
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
        return parent::find()->where(['status' => self::STATUS_PENDING]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'meter_ratio' => 'Meter Ratio',
            'asset_id' => 'Asset ID',
            'task_type' => 'Task Type',
            'ppm_service_id' => 'Ppm Service ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'remarks' => 'Remarks',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(LocationEquipments::className(), ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPpmService()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'ppm_service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(PlantPpmTasks::className(), ['id' => 'task_id']);
    }
}
