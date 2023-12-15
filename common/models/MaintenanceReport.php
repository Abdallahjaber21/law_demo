<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\behaviors\RandomTokenBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "maintenance_report".
 *
 * @property integer $id
 * @property integer $location_id
 * @property integer $technician_id
 * @property integer $month
 * @property integer $year
 * @property string $report
 * @property string $random_token
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Location $location
 * @property Technician $technician
 *
 * @property string $status_label
 * @property label $status_list
 */
class MaintenanceReport extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'technician_id', 'month', 'year', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['report', 'random_token'], 'string', 'max' => 255],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['technician_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'random_token' => [
                'class'      => RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'timestamp'    => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable'    => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'       => [
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
            'id'            => 'ID',
            'location_id'   => 'Location ID',
            'technician_id' => 'Technician ID',
            'month'         => 'Month',
            'year'          => 'Year',
            'report'        => 'Report',
            'random_token'  => 'Random Token',
            'status'        => 'Status',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
            'created_by'    => 'Created By',
            'updated_by'    => 'Updated By',
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

    /**
     * @return ActiveQuery
     */
    public function getTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'technician_id']);
    }

    public function getFileUrl()
    {
        return Yii::getAlias("@staticWeb/upload/maintenance_reports/{$this->year}/{$this->month}/{$this->id}_{$this->random_token}.pdf");
    }

    public function getClientFileUrl()
    {
        return Yii::getAlias("@staticWeb/upload/maintenance_reports/client/{$this->year}/{$this->month}/{$this->id}_{$this->random_token}.pdf");
    }

    public function getFilePath()
    {
        return Yii::getAlias("@static/upload/maintenance_reports/{$this->year}/{$this->month}/{$this->id}_{$this->random_token}.pdf");
    }

    public function getClientFilePath()
    {
        return Yii::getAlias("@static/upload/maintenance_reports/client/{$this->year}/{$this->month}/{$this->id}_{$this->random_token}.pdf");
    }


    public function getPreviewFileUrl()
    {
        return Yii::getAlias("@staticWeb/upload/maintenance_reports/preview/{$this->year}/{$this->month}/{$this->random_token}.pdf");
    }
    public function getPreviewFilePath()
    {
        return Yii::getAlias("@static/upload/maintenance_reports/preview/{$this->year}/{$this->month}/{$this->random_token}.pdf");
    }

    public function fields()
    {
        return [
            'id',
            'form_name'     => function (MaintenanceReport $model) {
                return MaintenanceReport::getFormName($model->report);
            },
            'url'           => function (MaintenanceReport $model) {
                return $model->getFileUrl();
            },
            'location_name' => function (MaintenanceReport $model) {
                return $model->location->name;
            },
            'created_at'    => function (MaintenanceReport $model) {
                return Yii::$app->formatter->asDate($model->created_at);
            },
        ];
    }

    public static function getFormName($form)
    {
        $formNames = [
            "A01" => 'General Service',
        ];
        return !empty($formNames[$form]) ? $formNames[$form] : 'Maintenance Service';
    }
}
