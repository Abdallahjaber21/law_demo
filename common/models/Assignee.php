<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "assignee".
 *
 * @property int $id
 * @property int $repair_request_id
 * @property int $user_id
 * @property string $description
 * @property int $status
 * @property string $datetime
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property RepairRequest $repairRequest
 * @property Technician $user
 *
 * @property string $status_label
 * @property label $status_list
 */
class Assignee extends \yii\db\ActiveRecord
{

    public $acceptance_status;

    const STATUS_BUSY = 10;
    const STATUS_BREAK = 20;
    const STATUS_HOURLY_LEAVE = 30;
    const STATUS_FREE = 40;
    const STATUS_ASSIGNED = 50;
    const STATUS_ACCEPTED = 60;
    const STATUS_REJEJCTED = 70;

    const STATUS_CHECKED_OUT = 80;
    const STATUS_ON_ROAD = 90;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'assignee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['repair_request_id', 'user_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['description', 'datetime'], 'string'],
            [['created_at', 'updated_at', 'acceptance_status'], 'safe'],
            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'repair_request_id' => Yii::t('app', 'Repair Request ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'datetime' => Yii::t('app', 'Date Time'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression("now()"),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_BUSY => Yii::t("app", "Checked In"),
                    self::STATUS_BREAK => Yii::t("app", "Break"),
                    self::STATUS_HOURLY_LEAVE => Yii::t("app", "Hourly leave"),
                    self::STATUS_FREE => Yii::t("app", "Free"),
                    self::STATUS_ASSIGNED => Yii::t("app", "Assigned"),
                    self::STATUS_CHECKED_OUT => Yii::t("app", "Checked Out"),
                    self::STATUS_ON_ROAD => Yii::t("app", "On Road"),
                ]
            ],
            'acceptance_status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'acceptance_status',
                'options' => [
                    self::STATUS_ACCEPTED => Yii::t("app", "Accepted"),
                    self::STATUS_REJEJCTED => Yii::t("app", "Rejected"),
                ]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'repair_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Technician::className(), ['id' => 'user_id']);
    }
    public static function getRepairRequestByAssignee($from, $to, $service_type)
    {
        $loggedUserDivisionId = Yii::$app->user->identity->division_id;
        if (!empty($loggedUserDivisionId)) {
            $totalCount = RepairRequest::find()
                ->where(['between', 'scheduled_at', $from, $to])
                ->andWhere(['service_type' => $service_type])
                ->andWhere(['division_id' => $loggedUserDivisionId])
                ->all()
                ->count();
        } else {
            $totalCount = RepairRequest::find()
                ->where(['between', 'scheduled_at', $from, $to])
                ->andWhere(['service_type' => $service_type])
                ->count();
        }
        return $totalCount;
    }
    public static function getCompletedRepairRequestByAssignee($from, $to, $service_type)
    {
        $loggedUserDivisionId = Yii::$app->user->identity->division_id;
        if (!empty($loggedUserDivisionId)) {
            $totalCount = RepairRequest::find()
                ->where(['between', 'scheduled_at', $from, $to])
                ->andWhere(['service_type' => $service_type])
                ->andWhere([
                    'status' => RepairRequest::STATUS_COMPLETED,
                    'division_id' => $loggedUserDivisionId,
                ])
                ->andWhere(['division_id' => $loggedUserDivisionId])
                ->all()
                ->count();
        } else {
            $totalCount = RepairRequest::find()
                ->where(['between', 'scheduled_at', $from, $to])
                ->andWhere([
                    'status' => RepairRequest::STATUS_COMPLETED,
                    'division_id' => $loggedUserDivisionId,
                ])
                ->andWhere(['service_type' => $service_type])
                ->count();
        }
        return $totalCount;
    }
}