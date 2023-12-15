<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property integer $technician_id
 * @property integer $repair_request_id
 * @property integer $type
 * @property string $title
 * @property string $description
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property RepairRequest $repairRequest
 * @property Technician $technician
 *
 * @property string $type_label
 * @property array $type_list
 * @property string $status_label
 * @property label $status_list
 */
class Log extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    // Status
    const TYPE_REPAIR_REQUEST = 10;
    const TYPE_ADMIN = 20;
    const TYPE_TECHNICIAN = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['technician_id', 'repair_request_id', 'type', 'status', 'created_by', 'updated_by'], 'integer'],
            [['title', 'description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
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
                'value' => gmdate("Y-m-d H:i:s"),
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
                    self::TYPE_REPAIR_REQUEST => Yii::t("app", "Request"),
                    self::TYPE_ADMIN => Yii::t("app", "Admin"),
                    self::TYPE_TECHNICIAN => Yii::t("app", "Technician"),
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
            'technician_id' => 'Technician ID',
            'repair_request_id' => 'Repair Request ID',
            'type' => 'Type',
            'title' => 'Title',
            'description' => 'Description',
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
    public function getRepairRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'repair_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }

    public static function AddLog($technician_id = null, $request_id, $type, $title, $description, $entity_status)
    {
        $model = new self();

        if (!empty($technician_id)) {
            $model->technician_id = $technician_id;
        }

        $model->repair_request_id = $request_id;
        $model->type = $type;
        $model->title = $title;
        $model->description = $description;
        $model->status = $entity_status;

        if (!$model->save()) {
            print_r($model->errors);
            exit;
        } else {
            if (empty($model->created_by)) {
                $model->created_by = -1;
                $model->updated_by = -1;
                $model->save();
            }
        }

        return true;
    }

    public function fields()
    {
        return [
            'id',
            'technician' => function ($model) {
                return @Technician::findOne($model->technician_id)->name;
            },
            'repair_request_id',
            'description',
            'created_at'
        ];
    }
}