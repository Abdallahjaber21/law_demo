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
 * This is the model class for table "maintenance_task".
 *
 * @property integer $id
 * @property integer $maintenance_task_group_id
 * @property string $code
 * @property string $name
 * @property integer $task_order
 * @property double $duration
 * @property integer $m_1_a
 * @property integer $m_2_a
 * @property integer $m_3_a
 * @property integer $m_4_a
 * @property integer $m_5_a
 * @property integer $m_6_a
 * @property integer $m_7_a
 * @property integer $m_8_a
 * @property integer $m_9_a
 * @property integer $m_10_a
 * @property integer $m_11_a
 * @property integer $m_12_a
 * @property integer $m_1_b
 * @property integer $m_2_b
 * @property integer $m_3_b
 * @property integer $m_4_b
 * @property integer $m_5_b
 * @property integer $m_6_b
 * @property integer $m_7_b
 * @property integer $m_8_b
 * @property integer $m_9_b
 * @property integer $m_10_b
 * @property integer $m_11_b
 * @property integer $m_12_b
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property MaintenanceTaskGroup $maintenanceTaskGroup
 *
 * @property string $status_label
 * @property label $status_list
 */
class MaintenanceTask extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance_task';
    }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['maintenance_task_group_id', 'task_order', 'status', 'created_by', 'updated_by'], 'integer'],
            [['duration'], 'number'],
            [['m_1_a', 'm_2_a', 'm_3_a', 'm_4_a', 'm_5_a', 'm_6_a', 'm_7_a', 'm_8_a', 'm_9_a', 'm_10_a', 'm_11_a', 'm_12_a', 'm_1_b', 'm_2_b', 'm_3_b', 'm_4_b', 'm_5_b', 'm_6_b', 'm_7_b', 'm_8_b', 'm_9_b', 'm_10_b', 'm_11_b', 'm_12_b'], 'boolean'],
            [['m_1_a', 'm_2_a', 'm_3_a', 'm_4_a', 'm_5_a', 'm_6_a', 'm_7_a', 'm_8_a', 'm_9_a', 'm_10_a', 'm_11_a', 'm_12_a'], 'default', 'value' => true],
            [['m_1_b', 'm_2_b', 'm_3_b', 'm_4_b', 'm_5_b', 'm_6_b', 'm_7_b', 'm_8_b', 'm_9_b', 'm_10_b', 'm_11_b', 'm_12_b'], 'default', 'value' => false],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['maintenance_task_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaintenanceTaskGroup::className(), 'targetAttribute' => ['maintenance_task_group_id' => 'id']],
        ];
    }
    //    use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    //    public static function find()
    //    {
    //        return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    //    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression("now()"),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'maintenance_task_group_id' => Yii::t('app', 'Maintenance Task Group'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'task_order' => Yii::t('app', 'Task Order'),
            'duration' => Yii::t('app', 'Duration'),
            'm_1_a' => Yii::t('app', '1A'),
            'm_2_a' => Yii::t('app', '2A'),
            'm_3_a' => Yii::t('app', '3A'),
            'm_4_a' => Yii::t('app', '4A'),
            'm_5_a' => Yii::t('app', '5A'),
            'm_6_a' => Yii::t('app', '6A'),
            'm_7_a' => Yii::t('app', '7A'),
            'm_8_a' => Yii::t('app', '8A'),
            'm_9_a' => Yii::t('app', '9A'),
            'm_10_a' => Yii::t('app', '10A'),
            'm_11_a' => Yii::t('app', '11A'),
            'm_12_a' => Yii::t('app', '12A'),
            'm_1_b' => Yii::t('app', '1B'),
            'm_2_b' => Yii::t('app', '2B'),
            'm_3_b' => Yii::t('app', '3B'),
            'm_4_b' => Yii::t('app', '4B'),
            'm_5_b' => Yii::t('app', '5B'),
            'm_6_b' => Yii::t('app', '6B'),
            'm_7_b' => Yii::t('app', '7B'),
            'm_8_b' => Yii::t('app', '8B'),
            'm_9_b' => Yii::t('app', '9B'),
            'm_10_b' => Yii::t('app', '10B'),
            'm_11_b' => Yii::t('app', '11B'),
            'm_12_b' => Yii::t('app', '12B'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenanceTaskGroup()
    {
        return $this->hasOne(MaintenanceTaskGroup::className(), ['id' => 'maintenance_task_group_id']);
    }

    public function fields()
    {
        return [
            'id',
            'code',
            'name'
        ];
    }
}
