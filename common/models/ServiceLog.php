<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "service_log".
 *
 * @property integer $id
 * @property integer $service_id
 * @property string $user_name
 * @property string $log_message
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property RepairRequest $service
 *
 * @property string $status_label
 * @property label $status_list
 */
class ServiceLog extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_name', 'log_message'], 'string'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable' => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'service_id'  => 'Service ID',
            'user_name'   => 'User Name',
            'log_message' => 'Log Message',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
            'created_by'  => 'Created By',
            'updated_by'  => 'Updated By',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'service_id']);
    }

    public static function log($id, $message, $username = null)
    {
        if (empty($username)) {
            if (!Yii::$app->user->isGuest) {
                $user = Yii::$app->user->identity;
                $username = $user->name;
            }
        }
        $serviceLog = ServiceLog::find()
            ->where(['service_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        if (!empty($serviceLog) && $serviceLog->log_message == $message) {
            Yii::error("service log message '{$message}' already saved");
            return true;
        }
        $serviceLog = new ServiceLog([
            'service_id'  => $id,
            'log_message' => $message,
            'user_name'   => $username
        ]);
        $serviceLog->save();
    }
}
