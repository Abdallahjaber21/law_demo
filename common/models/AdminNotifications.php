<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "admin_notifications".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $technician_id
 * @property integer $seen
 * @property integer $type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property RepairRequest $request
 * @property Technician $technician
 *
 * @property string $type_label
 * @property array $type_list
 * @property string $status_label
 * @property label $status_list
 */
class AdminNotifications extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    // Status
    const TYPE_STATUS = 10;
    const TYPE_COORDINATES = 20;
    const TYPE_COMPLAINT = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'technician_id', 'type', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['seen'], 'string', 'max' => 1],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['request_id' => 'id']],
            [['technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['technician_id' => 'id']],
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
            'type' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'type',
                'options' => [
                    self::TYPE_STATUS => Yii::t("app", "Status"),
                    self::TYPE_COORDINATES => Yii::t("app", "Coorinates"),
                    self::TYPE_COMPLAINT => Yii::t("app", "Complaint"),
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
            'request_id' => 'Request ID',
            'technician_id' => 'Technician ID',
            'seen' => 'Seen',
            'type' => 'Type',
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
    public function getRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }

    public static function getMessage($type, $request_id, $technician_id, $status)
    {

        $status_label = (new RepairRequest())->status_list[$status];
        $technician_name = Technician::findOne($technician_id)->name;

        if ($type == self::TYPE_STATUS) {

            if ($status == RepairRequest::STATUS_DRAFT) {
                return "{$technician_name} Created Work <strong>#{$request_id}</strong>, Status Set To: <strong>{$status_label}</strong>";
            }

            return "{$technician_name} Changed Work <strong>#{$request_id}</strong> Status To: <strong>{$status_label}</strong>";
        } else if ($type == self::TYPE_COORDINATES) {
            return "{$technician_name} Reported Missing Coordinates for Work Order : <strong>#{$request_id}</strong>";
        }
    }

    public function getActionUrl()
    {
        $type = $this->type;
        if (!empty($type)) {
            if ($type == self::TYPE_STATUS) {
                return ['/notification/click-admin', 'id' => $this->id];
            } else if ($type == self::TYPE_COORDINATES) {
                return ['/notification/click-coordinate', 'id' => $this->id];
            }
        }
    }
}