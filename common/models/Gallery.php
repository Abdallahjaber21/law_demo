<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "gallery".
 *
 * @property integer $id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $description
 *
 * @property Image[] $images
 * @property Log[] $logs
 * @property Media[] $media
 * @property RepairRequest[] $repairRequests
 *
 * @property string $status_label
 * @property label $status_list
 */
class Gallery extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gallery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
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
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
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
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'description' => 'Description',
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
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['gallery_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['gallery_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::className(), ['gallery_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['gallery_id' => 'id']);
    }
}
