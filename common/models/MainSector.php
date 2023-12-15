<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\UserAuditBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "main_sector".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property Division $division

 * 
 * @property string $status_label
 * @property label $status_list
 * @property Sector[] $sectors
 * @property Sector[] $sectorsArray
 * @property Locaiton[] $locations
 */
class MainSector extends \yii\db\ActiveRecord
{
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;
    public static function tableName()
    {
        return 'main_sector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by', 'division_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],

        ];
    }

    public function behaviors()
    {
        return [
            'userAudit' => UserAuditBehavior::class,
            'timestamp' => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' =>  gmdate("Y-m-d h:i:s"),
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
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),

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
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'division_id' => 'Division',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectors()
    {
        return $this->hasMany(Sector::className(), ['main_sector_id' => 'id']);
    }
    public static function getAllSectors($parentId)
    {

        $sector = @Sector::findOne($parentId)->mainSector;
        if (!empty($sector))
            return $sector->hasMany(Sector::className(), ['main_sector_id' => 'id'])->orderBy(['name' => SORT_ASC])->all();
        else return null;
    }

    public function getSectorsArray()
    {
        return $this->hasMany(Sector::className(), ['main_sector_id' => 'id'])->asArray();
    }

    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }
}
