<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "equipment_path".
 *
 * @property int $id
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $segment_path_id
 * @property int $equipment_id
 * @property array $value
 *
 * @property Equipment $equipment
 * @property SegmentPath $segmentPath
 */
class EquipmentPath extends \yii\db\ActiveRecord
{
    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_path';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by', 'segment_path_id', 'equipment_id'], 'integer'],
            [['created_at', 'updated_at', 'value'], 'safe'],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
            [['segment_path_id'], 'exist', 'skipOnError' => true, 'targetClass' => SegmentPath::className(), 'targetAttribute' => ['segment_path_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp'      => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable'      => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'         => [
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
            'id' => 'ID',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'segment_path_id' => 'Segment Path',
            'equipment_id' => 'Equipment',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['id' => 'equipment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegmentPath()
    {
        return $this->hasOne(SegmentPath::className(), ['id' => 'segment_path_id']);
    }
}
