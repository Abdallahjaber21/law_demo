<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equipment_maintenance_barcodes".
 *
 * @property int $id
 * @property int $equipment_id
 * @property string $location
 * @property string $barcode
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $code
 *
 * @property BarcodeScan[] $barcodeScans
 * @property CompletedMaintenanceTask[] $completedMaintenanceTasks
 * @property Equipment $equipment
 */
class EquipmentMaintenanceBarcodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_maintenance_barcodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['equipment_id', 'status', 'created_by', 'updated_by', 'code'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['location', 'barcode'], 'string', 'max' => 255],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'equipment_id' => Yii::t('app', 'Equipment ID'),
            'location' => Yii::t('app', 'Location'),
            'barcode' => Yii::t('app', 'Barcode'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarcodeScans()
    {
        return $this->hasMany(BarcodeScan::className(), ['barcode_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompletedMaintenanceTasks()
    {
        return $this->hasMany(CompletedMaintenanceTask::className(), ['equipment_maintenance_barcode_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['id' => 'equipment_id']);
    }
}
