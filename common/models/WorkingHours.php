<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "working_hours".
 *
 * @property integer $id
 * @property string $year_month
 * @property array $daily_hours
 * @property array $holidays
 * @property double $total_hours
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property string $status_label
 * @property label $status_list
 */
class WorkingHours extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_ENABLED = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'working_hours';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year_month'], 'required'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['daily_hours', 'holidays', 'created_at', 'updated_at'], 'safe'],
            [['total_hours'], 'number'],
            [['year_month'], 'string'],
            [['year_month'], 'unique'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new \yii\db\Expression("now()"),
            ],
            'blameable' => [
                'class'              => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                ]
            ],
        ];
    }

    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        $this->holidays = !empty($this->holidays) ? Json::decode($this->holidays) : $this->holidays;
        $this->daily_hours = !empty($this->daily_hours) ? Json::decode($this->daily_hours) : $this->daily_hours;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'year_month'  => 'Year Month',
            'daily_hours' => 'Daily Hours',
            'holidays'    => 'Holidays',
            'total_hours' => 'Total Hours',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
            'created_by'  => 'Created By',
            'updated_by'  => 'Updated By',
        ];
    }
}
