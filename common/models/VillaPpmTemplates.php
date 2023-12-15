<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "villa_ppm_templates".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sector_id
 * @property integer $location_id
 * @property integer $category_id
 * @property integer $asset_id
 * @property integer $project_id
 * @property integer $frequency
 * @property integer $repeating_condition
 * @property string $note
 * @property string $path
 * @property string $team_members
 * @property string $tasks
 * @property string $next_scheduled_date
 * @property string $starting_date_time
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property RepairRequest[] $repairRequests
 * @property LocationEquipments $asset
 * @property Category $category
 * @property Location $location
 * @property Project $project
 * @property Sector $sector
 *
 * @property string $status_label
 * @property label $status_list
 */
class VillaPpmTemplates extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    const REPEATING_FIXED_DATE = 10;
    const REPEATING_WHEN_COMPLETED = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'villa_ppm_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sector_id', 'category_id', 'project_id', 'frequency', 'repeating_condition', 'status', 'created_by', 'updated_by'], 'integer'],
            [['next_scheduled_date', 'created_at', 'updated_at', 'location_id', 'asset_id', 'team_members', 'tasks', 'project_id'], 'safe'],
            [['name', 'note', 'path'], 'string', 'max' => 255],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocationEquipments::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sector::className(), 'targetAttribute' => ['sector_id' => 'id']],
            [['name', 'sector_id', 'category_id', 'frequency', 'repeating_condition', 'status', 'starting_date_time', 'path'], 'required']
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
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Stopped"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            'repeating_condition' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'repeating_condition',
                'options' => [
                    self::REPEATING_FIXED_DATE => Yii::t("app", "Fixed Date"),
                    self::REPEATING_WHEN_COMPLETED => Yii::t("app", "When Completed"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
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
            'id' => 'ID',
            'name' => 'Name',
            'sector_id' => 'Sector',
            'location_id' => 'Location',
            'category_id' => 'Category',
            'asset_id' => 'Asset',
            'project_id' => 'Project',
            'frequency' => 'Duration',
            'repeating_condition' => 'Repeating Condition',
            'note' => 'Note',
            'team_members' => 'Team Members',
            'next_scheduled_date' => 'Next Scheduled Date',
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
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(LocationEquipments::className(), ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

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
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    public static function getCommaSeperatedValues($array, $className, $set_attribute, $get_attribute, $relation = null)
    {
        $out = [];

        if (!empty($array)) {
            foreach (explode(',', $array) as $datum) {
                $model = $className::find()->where([$set_attribute => $datum])->one();

                if (!empty($relation)) {
                    $out[] = $model->$relation->$get_attribute;
                } else {
                    $out[] = $model->$get_attribute;
                }
            }
        }

        return implode(',', $out);
    }
}
