<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;

/**
 * This is the model class for table "coordinates_issue".
 *
 * @property integer $id
 * @property integer $location_id
 * @property integer $reported_by
 * @property string $old_latitude
 * @property string $old_longitude
 * @property string $new_latitude
 * @property string $new_longitude
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Location $location
 * @property Technician $reportedBy
 *
 * @property string $status_label
 * @property label $status_list
 */
class CoordinatesIssue extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_PENDING = 10;
    const STATUS_APPROVED = 20;
    const STATUS_REJECTED = 30;


    public static $return_fields = 10;
    const FIELDS_EXPORT = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coordinates_issue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'reported_by', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['old_latitude', 'old_longitude', 'new_latitude', 'new_longitude'], 'string', 'max' => 255],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['reported_by'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['reported_by' => 'id']],
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
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_PENDING => Yii::t("app", "Pending"),
                    self::STATUS_APPROVED => Yii::t("app", "Approved"),
                    self::STATUS_REJECTED => Yii::t("app", "Rejected"),
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

    // public static function findEnabled()
    // {
    //     return parent::find()->where(['status' => self::STATUS_ENABLED]);
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location_id' => 'Location ID',
            'reported_by' => 'Reported By',
            'old_latitude' => 'Old Latitude',
            'old_longitude' => 'Old Longitude',
            'new_latitude' => 'New Latitude',
            'new_longitude' => 'New Longitude',
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
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportedBy()
    {
        return $this->hasOne(Technician::className(), ['id' => 'reported_by']);
    }

    public function fields()
    {
        if (self::$return_fields == self::FIELDS_EXPORT) {
            return [
                'location_name' => function ($model) {
                    return @$model->location->name;
                },
                'reported_by' => function ($model) {
                    return @$model->reportedBy->name;
                },
                'old_latitude',
                'old_longitude',
                'new_latitude',
                'new_longitude',
                'status' => function ($model) {
                    return $model->status_label;
                },
                'date' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at);
                },
            ];
        }
        return parent::fields();
    }
}