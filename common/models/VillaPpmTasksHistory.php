<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "villa_ppm_tasks_history".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $asset_id
 * @property integer $ppm_service_id
 * @property integer $frequency
 * @property string $remarks
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property LocationEquipments $asset
 * @property RepairRequest $ppmService
 * @property VillaPpmTasks $task
 *
 * @property string $status_label
 * @property label $status_list
 */
class VillaPpmTasksHistory extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    const TASK_STATUS_DONE = 10;
    const TASK_STATUS_NOT_DONE = 20;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'villa_ppm_tasks_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'asset_id', 'ppm_service_id', 'frequency', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['remarks'], 'string', 'max' => 255],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocationEquipments::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['ppm_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['ppm_service_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => VillaPpmTasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            // 'status' => [
            //     'class' => \common\behaviors\OptionsBehavior::className(),
            //     'attribute' => 'status',
            //     'options' => [
            //         self::STATUS_ENABLED => Yii::t("app", "Active"),
            //         self::STATUS_DISABLED => Yii::t("app", "Inactive"),
            //         //self::STATUS_DELETED => Yii::t("app", "Deleted"),
            //     ]
            // ],
            'status' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
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
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'asset_id' => 'Asset ID',
            'ppm_service_id' => 'Ppm Service ID',
            'frequency' => 'Frequency',
            'remarks' => 'Remarks',
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
        return $this->hasOne(VillaPpmTasks::className(), ['id' => 'task_id']);
    }
}