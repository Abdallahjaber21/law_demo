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
 * This is the model class for table "shifts".
 *
 * @property int $id
 * @property string $name
 * @property string $from_hour
 * @property string $to_hour
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property label $status_list
 * @property State $state
 *
 * @property TechnicianShift[] $technicianShifts
 */
class Shifts extends \yii\db\ActiveRecord
{
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shifts';
    }
    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_hour', 'to_hour', 'created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['name'], 'string', 'max' => 255],
        ];
    }

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
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'from_hour' => Yii::t('app', 'From Hour'),
            'to_hour' => Yii::t('app', 'To Hour'),
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
    public function getTechnicianShifts()
    {
        return $this->hasMany(TechnicianShift::className(), ['shift_id' => 'id']);
    }
}
