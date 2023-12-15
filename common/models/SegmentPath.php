<?php

namespace common\models;

use admin\models\Model;
use common\behaviors\OptionsBehavior;
use common\behaviors\UserAuditBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "segment_path".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property array $value
 * @property int $sector_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $description
 *
 * @property EquipmentPath[] $equipmentPaths
 * @property Location[] $locations
 * @property Sector $sector
 */
class SegmentPath extends \yii\db\ActiveRecord
{

    public $division_id;

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segment_path';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'created_at', 'updated_at', 'division_id'], 'safe'],
            [['sector_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['name', 'description', 'code'], 'string', 'max' => 255],
            [['sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sector::className(), 'targetAttribute' => ['sector_id' => 'id']],
            [['name', 'sector_id', 'value', 'code'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'userAudit' => UserAuditBehavior::class,
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
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),
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
            'name' => 'Name',
            'code' => 'Code',
            'value' => 'Equipment Path',
            'sector_id' => 'Sector',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'description' => 'Description',
            'division_id' => 'Division'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentPaths()
    {
        return $this->hasMany(EquipmentPath::className(), ['segment_path_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['segment_path_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    public static function getLayersValue($layers_array, $seperator = null, $html = false)
    {
        $out = [];

        $layers_array = is_array($layers_array) ? $layers_array : Json::decode($layers_array);

        foreach ($layers_array as $arr) {
            if (!empty($html) && $html) {
                $out[] = $arr['layer'] . ':';
            } else {
                $out[] = $arr['layer'];
            }
        }

        return implode(!empty($seperator) ? $seperator : ' - ', $out);
    }

    public static function getLayersArrayModels($layers_json)
    {
        $out = [];

        $layers_json = is_array($layers_json) ? $layers_json : Json::decode($layers_json);

        foreach ($layers_json as $j) {
            $model = new SegmentPath();
            $model->id = $j['id'] + 1;
            $model->value = $j['layer'];

            $out[] = $model;
        }

        return $out;
    }

    public static function GetJsonSegment($segment_pathes_model)
    {
        $json_values = [];

        foreach ($segment_pathes_model as $index => $m) {

            if (!empty($m->value)) {
                $json_values[] = [
                    'id' => $index,
                    'layer' => ucfirst($m->value)
                ];
            }
        }

        return Json::encode($json_values);
    }
}
