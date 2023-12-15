<?php

namespace common\models;

use Yii;
use common\behaviors\OptionsBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "technician_shift".
 *
 * @property int $id
 * @property int $technician_id
 * @property int $shift_id
 * @property string $date
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Shift $shift
 * @property Technician $technician
 */
class TechnicianShift extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 20;
    const STATUS_DISABLED = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'technician_shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['technician_id', 'shift_id'], 'required'],
            [['technician_id', 'shift_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'id']],
            [['technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['technician_id' => 'id']],
        ];
    }
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => gmdate("Y-m-d h:i:s"),
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'technician_id' => Yii::t('app', 'Technician ID'),
            'shift_id' => Yii::t('app', 'Shift ID'),
            'date' => Yii::t('app', 'Date'),
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
    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }
}
