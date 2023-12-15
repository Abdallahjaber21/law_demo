<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "repair_request_files".
 *
 * @property int $id
 * @property int $repair_request_id
 * @property string $old_file
 * @property string $new_file
 * @property string $type
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property RepairRequest $repairRequest
 */
class RepairRequestFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'repair_request_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {

        return [
            [['repair_request_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 255],
            [['old_file', 'new_file'], 'file'], // Adjust the allowed file extensions as needed

            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_request_id' => 'Repair Request ID',
            'old_file' => 'Old File',
            'new_file' => 'New File',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'repair_request_id']);
    }
}
