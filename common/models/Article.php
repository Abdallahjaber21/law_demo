<?php

namespace common\models;

use common\behaviors\ImageUploadBehavior;
use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $subtitle
 * @property string $content
 * @property string $image
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $random_token
 * @property string $category
 * @property string $external_link
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $status_label
 * @property label $status_list
 */
class Article extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
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
            [['content'], 'string'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            ['image', 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 2],
            [['title', 'subtitle', 'random_token', 'category'], 'string', 'max' => 255],
            [['external_link'], 'url', 'skipOnEmpty' => true]
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
            'random_token' => [
                'class'      => \common\behaviors\RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'timestamp'    => [
                'class'              => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new \yii\db\Expression("now()"),
            ],
            'blameable'    => [
                'class'              => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'       => [
                'class'     => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'image'        => [
                'class'                 => \common\behaviors\ImageUploadBehavior::className(),
                'attribute'             => 'image',
                'thumbs'                => [
                    'thumb'      => ['width' => 250, 'height' => 250],
                    'thumb_1024' => ['width' => 1024, 'height' => 1024],
                ],
                'createThumbsOnRequest' => true,
                'resize'                => ImageUploadBehavior::RESIZE_CONTAIN,
                'filePath'              => '@static/upload/images/article/article_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl'               => '@staticWeb/upload/images/article/article_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath'             => '@static/upload/images/article/[[profile]]/article_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl'              => '@staticWeb/upload/images/article/[[profile]]/article_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
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
            'id'           => Yii::t('app', 'ID'),
            'title'        => Yii::t('app', 'Title'),
            'subtitle'     => Yii::t('app', 'Subtitle'),
            'content'      => Yii::t('app', 'Content'),
            'image'        => Yii::t('app', 'Image'),
            'status'       => Yii::t('app', 'Status'),
            'created_at'   => Yii::t('app', 'Created At'),
            'updated_at'   => Yii::t('app', 'Updated At'),
            'created_by'   => Yii::t('app', 'Created By'),
            'updated_by'   => Yii::t('app', 'Updated By'),
            'random_token' => Yii::t('app', 'Random Token'),
        ];
    }

    public function attributeHints()
    {
        $hints = parent::attributeHints(); // TODO: Change the autogenerated stub
        $hints['external_link'] = "Optional, keep empty to show article in app";
        return $hints;
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
            "title",
            "subtitle",
            "content",
            "image_thumb_1024_url",
            "image_url",
            "category",
            "external_link",
        ];
    }
}
