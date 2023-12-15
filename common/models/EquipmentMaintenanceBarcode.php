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
 * This is the model class for table "equipment_maintenance_barcodes".
 *
 * @property integer $id
 * @property integer $equipment_id
 * @property string $location
 * @property string $barcode
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $code
 *
 * @property Equipment $equipment
 *
 * @property string $status_label
 * @property label $status_list
 */
class EquipmentMaintenanceBarcode extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_maintenance_barcodes';
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
            [['barcode', 'location', 'equipment_id'], 'required'],
            [['equipment_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['barcode', 'location'], 'safe'],
            [['code'], 'integer'],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
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
            'equipment_id' => Yii::t('app', 'Equipment'),
            'barcode'      => Yii::t('app', 'Barcode'),
            'location'     => Yii::t('app', 'Location'),
            'status'       => Yii::t('app', 'Status'),
            'created_at'   => Yii::t('app', 'Created At'),
            'updated_at'   => Yii::t('app', 'Updated At'),
            'created_by'   => Yii::t('app', 'Created By'),
            'updated_by'   => Yii::t('app', 'Updated By'),
            'code'         => Yii::t('app', 'Code'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['id' => 'equipment_id']);
    }


    public function fields()
    {
        return [
            "id",
            "location",
            "barcode",
        ];
    }


    public static $codeDescMap = [
        "29"  => "BV",
        "41"  => "B. ST",
        "16"  => "B.TSD",
        "42"  => "B. MS",
        "17"  => "C.ARM",
        "18"  => "C.OPE",
        "19"  => "C.PANEL",
        "20"  => "SLING",
        "21"  => "C.TOP",
        "22"  => "COMP. SH",
        "11"  => "CP",
        "49"  => "CP",
        "51"  => "CP",
        "61"  => "CP",
        "31"  => "CP",
        "71"  => "CP",
        "23"  => "CWT",
        "24"  => "D.PUL",
        "52"  => "ELEV. ASSY",
        "25"  => "LD",
        "12"  => "ERS",
        "250" => "BOT. LD",
        "91"  => "G. OPR",
        "53"  => "HYD. ASSY",
        "28"  => "CYLINDER",
        "27"  => "JACK",
        "32"  => "KEY SW",
        "44"  => "L.HDR",
        "54"  => "CHASSIS",
        "43"  => "L.CP",
        "46"  => "TM",
        "62"  => "MD & RS",
        "33"  => "MOTOR",
        "14"  => "GOV",
        "26"  => "PIT",
        "45"  => "R.HDR",
        "48"  => "U.MS",
        "81"  => "SUS LADDER",
        "55"  => "SUS PLATFORM",
        "15"  => "T.TSD",
        "13"  => "TM",
        "47"  => "U.CP",
        "10"  => "LOG",
        "50"  => "LOG",
        "40"  => "LOG",
        "70"  => "LOG",
        "80"  => "LOG",
        "60"  => "LOG",
        "90"  => "LOG",
        "30"  => "LOG",
    ];

    public static function codeDescMap($code)
    {
        return !empty(self::$codeDescMap[$code]) ? self::$codeDescMap[$code] : $code;
    }


    public static $elevatorCodes = [
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        250,
    ];
    public static $escalatorCodes = [
        40,
        41,
        42,
        43,
        44,
        45,
        46,
        47,
        48,
        49,
    ];
    public static $bmuCodes = [
        50,
        51,
        52,
        53,
        54,
        55,
    ];
    public static $rdCodes = [
        60,
        61,
        62,
    ];
    public static $gdCodes = [
        70,
        71,
    ];
    public static $srCodes = [
        30,
        31,
        32,
        33,
    ];
    public static $ladderCodes = [
        80,
        81,
    ];
    public static $sgCodes = [
        90,
        91,
    ];


    public static $codeLocationsMap = null;
    public static function getBarcodeFullLocationNames()
    {
        if (!empty(self::$codeLocationsMap)) {
            return self::$codeLocationsMap;
        }
        $locationNames = EquipmentMaintenanceBarcode::find()
            ->select(['code', 'location'])
            ->distinct()
            ->asArray()
            ->all();
        $result  = [];
        foreach ($locationNames as $index => $locationName) {
            if (empty($result[$locationName['code']])) {
                $result[$locationName['code']] = [];
            }
            $result[$locationName['code']][] = $locationName['location'];
        }
        Yii::error($result);
        self::$codeLocationsMap = $result;
        return $result;
    }
}
