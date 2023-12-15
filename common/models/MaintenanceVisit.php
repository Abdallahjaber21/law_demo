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
 * This is the model class for table "maintenance_visit".
 *
 * @property integer $id
 * @property integer $location_id
 * @property integer $technician_id
 * @property string $checked_in
 * @property integer $status
 * @property string $checked_out
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Location $location
 * @property Technician $technician
 * @property BarcodeScan $barcodeScans
 *
 * @property string $status_label
 * @property label $status_list
 */
class MaintenanceVisit extends ActiveRecord
{

    // Status
    const STATUS_ENABLED = 10;
    const STATUS_COMPLETED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance_visit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'technician_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['checked_in', 'checked_out', 'created_at', 'updated_at'], 'safe'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
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
                    self::STATUS_ENABLED    => Yii::t("app", "Active"),
                    self::STATUS_COMPLETED => Yii::t("app", "Completed"),
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
            'id'            => 'ID',
            'location_id'   => 'Location ID',
            'technician_id' => 'Technician ID',
            'checked_in'    => 'Checked In',
            'status'        => 'Status',
            'checked_out'   => 'Checked Out',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
            'created_by'    => 'Created By',
            'updated_by'    => 'Updated By',
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBarcodeScans()
    {
        return $this->hasMany(BarcodeScan::className(), ['visit_id' => 'id']);
    }
}
