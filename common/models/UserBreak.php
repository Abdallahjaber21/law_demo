<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_break".
 *
 * @property int $id
 * @property int $technician_id
 * @property int $repair_request_id
 * @property string $date
 * @property string $from_break
 * @property string $to_break
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property RepairRequest $repairRequest
 * @property Technician $technician
 */
class UserBreak extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_break';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['technician_id', 'repair_request_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
            [['from_break', 'to_break'], 'string', 'max' => 255],
            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
            [['technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['technician_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'technician_id' => Yii::t('app', 'Technician ID'),
            'repair_request_id' => Yii::t('app', 'Repair Request ID'),
            'date' => Yii::t('app', 'Date'),
            'from_break' => Yii::t('app', 'From Break'),
            'to_break' => Yii::t('app', 'To Break'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
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
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }
}
