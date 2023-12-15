<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;

/**
 * This is the model class for table "problem".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $type
 *
 * @property string $status_label
 * @property label $status_list
 */
class Problem extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;

    //
    const TYPE_ALL = 99;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'problem';
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
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['type'], 'integer'],
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
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'type'      => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'type',
                'options'   => [
                    self::TYPE_ALL   => Yii::t("app", "All"),
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
            'id'         => Yii::t('app', 'ID'),
            'code'       => Yii::t('app', 'Code'),
            'name'       => Yii::t('app', 'Name'),
            'status'     => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'type'       => Yii::t('app', 'Type'),
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

    public function fields()
    {
        return [
            "id",
            "code",
            "name",
            "type",
            "type_label",
        ];
    }

    public static function mapEquipmentTypeToProbemType($equipment_type)
    {
        return Problem::TYPE_ALL;
        $mapArray = [
            //Equipment::EQUIPMENT_TYPE_CAPACITOR_PANEL                   => Problem::TYPE_ALL,
        ];
        if (empty($mapArray[$equipment_type])) {
            return Problem::TYPE_ALL;
        }
        return $mapArray[$equipment_type];
    }
}
