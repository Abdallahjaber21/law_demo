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
 * This is the model class for table "location_code".
 *
 * @property integer $id
 * @property integer $location_id
 * @property string $code
 * @property integer $usages_limit
 * @property integer $usages_count
 * @property integer $type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Location $location
 *
 * @property string $type_label
 * @property array $type_list
 * @property string $status_label
 * @property label $status_list
 */
class LocationCode extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;

    // Status
    const TYPE_RESIDENT = 10;
    const TYPE_DECISION_MAKER = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'usages_limit', 'usages_count', 'type', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
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
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'type'      => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'type',
                'options'   => [
                    self::TYPE_RESIDENT => Yii::t("app", "Resident"),
                    self::TYPE_DECISION_MAKER => Yii::t("app", "Decision Maker"),
                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }
    //    use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    //    public static function find()
    //    {
    //        return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    //    }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'location_id'  => 'Location ID',
            'code'         => 'Code',
            'usages_limit' => 'Usages Limit',
            'usages_count' => 'Usages Count',
            'type'         => 'Type',
            'status'       => 'Status',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At',
            'created_by'   => 'Created By',
            'updated_by'   => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
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

    /**
     * @return ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
}
