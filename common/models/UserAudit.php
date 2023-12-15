<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\behaviors\OptionsBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Json;
use common\config\includes\P;


/**
 * This is the model class for table "user_audit".
 *
 * @property int $id
 * @property int $user_id
 * @property int $class_id
 * @property int $entity_row_id
 * @property string $action
 * @property string $old_value
 * @property string $new_value
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Admin $user
 */
class UserAudit extends \yii\db\ActiveRecord
{
    const CLASS_NAME_LOCATION = 10;
    const CLASS_NAME_EQUIPMENT = 20;
    const CLASS_NAME_LOCATIONEQUIPMENT = 30;
    const CLASS_NAME_TECHNICIAN = 40;
    const CLASS_NAME_EQUIPMENTTYPE = 50;
    const CLASS_NAME_SEGMENTPATH = 60;
    const CLASS_NAME_PROFESSION = 70;
    const CLASS_NAME_CATEGORY = 80;
    const CLASS_NAME_MAINSECTOR = 90;
    const CLASS_NAME_SECTOR = 100;
    const CLASS_NAME_ADMIN = 110;

    public $action_changes = [];

    public function behaviors()
    {
        $behaviors = [
            TimestampBehavior::className() => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => gmdate("Y-m-d h:i:s"),
            ],
            'blameable' => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];

        $options = [];
        if (P::c(P::MANAGEMENT_LOCATION_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_LOCATION] = Yii::t("app", "Location");
        }
        if (P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_LOCATIONEQUIPMENT] = Yii::t("app", "Location Equipment");
        }
        if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_EQUIPMENT] = Yii::t("app", "Equipment");
        }
        if (P::c(P::ADMINS_ADMIN_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_ADMIN] = Yii::t("app", "Admin");
        }
        if (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_TECHNICIAN] = Yii::t("app", "Technician");
        }
        if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_EQUIPMENTTYPE] = Yii::t("app", "Equipment Type");
        }
        if (P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_SEGMENTPATH] = Yii::t("app", "Segment Path");
        }
        if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_PROFESSION] = Yii::t("app", "Profession");
        }
        if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_CATEGORY] = Yii::t("app", "Category");
        }
        if (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_MAINSECTOR] = Yii::t("app", "Main Sector");
        }
        if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_AUDIT)) {
            $options[self::CLASS_NAME_SECTOR] = Yii::t("app", "Sector");
        }
        $behaviors['class'] = [
            'class'     => OptionsBehavior::className(),
            'attribute' => 'class_id',
            'options'   => $options,
        ];

        return $behaviors;
    }
    public static function tableName()
    {
        return 'user_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'class_id', 'entity_row_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['action', 'old_value', 'new_value'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'class_id' => 'Class Name',
            'action' => 'Action',
            'entity_row_id' => 'Entity',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */


    public function getUser()
    {
        return $this->hasOne(Admin::className(), ['id' => 'user_id']);
    }
    public static function formatJsonAttributes($jsonString)
    {
        $userTimezone = Yii::$app->user->identity->timezone;
        $data = json_decode($jsonString, true);
        $formatted = [];

        if (is_array($data)) {
            foreach ($data as $attribute => $value) {
                if (($attribute === 'created_at' || $attribute === 'updated_at') && is_string($value)) {
                    $utcTimestamp = strtotime($value);
                    $userTimestamp = date('Y-m-d H:i:s', $utcTimestamp);
                    $userDatetime = new \DateTime($userTimestamp, new \DateTimeZone('UTC'));
                    $userDatetime->setTimezone(new \DateTimeZone($userTimezone));
                    $formatted[] = "<strong>$attribute</strong>: " . $userDatetime->format('Y-m-d H:i:s');
                } elseif (is_array($value) || is_object($value)) {
                    $formatted[] = "<strong>$attribute</strong>: " . json_encode($value);
                } else {
                    $formatted[] = "<strong>$attribute</strong>: $value";
                }
            }
        }

        return implode('<br>', $formatted);
    }
}
