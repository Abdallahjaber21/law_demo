<?php

namespace common\models;

use common\behaviors\DateFormatBehavior;
use common\behaviors\ImageUploadBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\RandomTokenBehavior;
use common\models\users\Admin;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "maintenance".
 *
 * @property integer $id
 * @property integer $equipment_id
 * @property integer $location_id
 * @property integer $technician_id
 * @property integer $status
 * @property integer $year
 * @property integer $month
 * @property string $random_token
 * @property string $note
 * @property string $customer_name
 * @property string $customer_signature
 * @property string $technician_signature
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $visit_number
 * @property boolean $report_generated
 * @property integer $remaining_barcodes
 * @property integer $number_of_barcodes
 * @property string $first_scan_at
 * @property string $completed_at
 * @property integer $report_id
 * @property integer $atl_status
 * @property string $atl_note
 * @property string $internal_notes
 * @property integer $complete_method
 * @property integer $duration
 * @property integer $completed_by_atl
 * @property string $hard_copy_report
 * @property integer $gallery_id
 * @property boolean $is_previously_rejected
 *
 * @property MaintenanceLog[] $maintenanceLogs
 * @property BarcodeScan[] $barcodeScans
 * @property Equipment $equipment
 * @property Location $location
 * @property Technician $technician
 * @property MaintenanceReport $report
 * @property Admin $completedByAtl
 * @property Gallery $gallery
 *
 * @property string $status_label
 * @property label $status_list
 */
class Maintenance extends ActiveRecord
{

    // Complete method
    const COMPLETE_SCAN_ALL = 10;
    const COMPLETE_PARTIAL = 20;
    // Status
    const STATUS_PENDING = 5;
    const STATUS_ASSIGNED = 10;
    const STATUS_START = 20;
    const STATUS_COMPLETE = 30;
    const STATUS_NOT_COMPLETE = 40;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_id', 'location_id', 'technician_id', 'status', 'year', 'month', 'created_by', 'updated_by'], 'integer'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['random_token', 'customer_name', 'customer_signature', 'technician_signature'], 'string', 'max' => 255],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['technician_id' => 'id']],
            [['visit_number'], 'string'],
            [['report_generated'], 'boolean'],
            [['report_generated'], 'default', 'value' => false],
            [['remaining_barcodes', 'number_of_barcodes'], 'integer'],
            [['first_scan_at', 'completed_at'], 'safe'],
            [['report_id'], 'integer'],
            [['atl_status'], 'integer'],
            [['atl_note'], 'string'],
            [['internal_notes'], 'string'],
            [['complete_method', 'completed_by_atl', 'duration'], 'integer'],
            [['complete_method'], 'default', 'value' => self::COMPLETE_PARTIAL],
            [['hard_copy_report'], 'string'],
            [['gallery_id'], 'integer'],
            [['is_previously_rejected'], 'boolean'],
            [['is_previously_rejected'], 'default', 'value' => false],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'random_token'    => [
                'class'      => RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'timestamp'       => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable'       => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'          => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_PENDING      => Yii::t("app", "Pending"),
                    self::STATUS_ASSIGNED     => Yii::t("app", "Assigned"),
                    self::STATUS_START        => Yii::t("app", "Started"),
                    self::STATUS_COMPLETE     => Yii::t("app", "Completed"),
                    self::STATUS_NOT_COMPLETE => Yii::t("app", "Not Complete"),
                ]
            ],
            'atl_status'      => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'atl_status',
                'options'   => [
                    self::STATUS_PENDING  => Yii::t("app", "Pending"),
                    self::STATUS_COMPLETE => Yii::t("app", "Completed"),
                ]
            ],
            'complete_method' => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'complete_method',
                'options'   => [
                    self::COMPLETE_SCAN_ALL => Yii::t("app", "Scan ALL"),
                    self::COMPLETE_PARTIAL  => Yii::t("app", "Partial"),
                ]
            ],

            'customer_signature'   => [
                'class'                 => ImageUploadBehavior::className(),
                'attribute'             => 'customer_signature',
                'createThumbsOnRequest' => true,
                'thumbs'                => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl'            => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath'              => '@static/upload/images/maintenance/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl'               => '@staticWeb/upload/images/maintenance/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath'             => '@static/upload/images/maintenance/[[profile]]/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl'              => '@staticWeb/upload/images/maintenance/[[profile]]/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            'technician_signature' => [
                'class'                 => ImageUploadBehavior::className(),
                'attribute'             => 'technician_signature',
                'createThumbsOnRequest' => true,
                'thumbs'                => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl'            => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath'              => '@static/upload/images/maintenance/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl'               => '@staticWeb/upload/images/maintenance/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath'             => '@static/upload/images/maintenance/[[profile]]/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl'              => '@staticWeb/upload/images/maintenance/[[profile]]/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            'hard_copy_report'     => [
                'class'                 => ImageUploadBehavior::className(),
                'attribute'             => 'hard_copy_report',
                'createThumbsOnRequest' => true,
                'thumbs'                => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl'            => Yii::getAlias('@staticWeb') . '/images/placeholder.jpg',
                'filePath'              => '@static/upload/images/maintenance/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl'               => '@staticWeb/upload/images/maintenance/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath'             => '@static/upload/images/maintenance/[[profile]]/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl'              => '@staticWeb/upload/images/maintenance/[[profile]]/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            "datetimeformatter2"   => [
                "class"      => DateFormatBehavior::className(),
                "type"       => DateFormatBehavior::TYPE_DATE_TIME,
                "attributes" => ['completed_at'],
                "format"     => "E, d LLL h:mma"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'equipment_id'         => 'Equipment',
            'report_id'            => 'Report',
            'location_id'          => 'Location',
            'technician_id'        => 'Technician',
            'status'               => 'Status',
            'year'                 => 'Year',
            'month'                => 'Month',
            'random_token'         => 'Random Token',
            'note'                 => 'Note',
            'customer_name'        => 'Customer Name',
            'customer_signature'   => 'Customer Signature',
            'technician_signature' => 'Technician Signature',
            'created_at'           => 'Created At',
            'updated_at'           => 'Updated At',
            'created_by'           => 'Created By',
            'updated_by'           => 'Updated By',
            'gallery_id'           => Yii::t('app', 'Gallery'),
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getBarcodeScans()
    {
        return $this->hasMany(BarcodeScan::className(), ['maintenance_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['id' => 'equipment_id']);
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

    /**
     * @return ActiveQuery
     */
    public function getCompletedByAtl()
    {
        return $this->hasOne(Admin::className(), ['id' => 'completed_by_atl']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(MaintenanceReport::className(), ['id' => 'report_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'gallery_id']);
    }

    public static $return_fields = 10;
    const FIELDS_DEFAULT = 10;
    const FIELDS_REPORT = 20;

    public function fields()
    {
        switch (self::$return_fields) {
            case self::FIELDS_REPORT:
                return [
                    'id',
                    'location_id',
                    'location_code'      => function (Maintenance $model) {
                        return $model->location->code;
                    },
                    'location_name'      => function (Maintenance $model) {
                        return $model->location->name;
                    },
                    'equipment_id',
                    'equipment_name'     => function (Maintenance $model) {
                        return $model->equipment->name;
                    },
                    'equipment_code'     => function (Maintenance $model) {
                        return $model->equipment->code;
                    },
                    'report_url'         => function (Maintenance $model) {
                        if (!empty($model->report)) {
                            return $model->report->getFileUrl();
                        }
                    },
                    'form_name'         => function (Maintenance $model) {
                        if (!empty($model->report)) {
                            return MaintenanceReport::getFormName($model->report->report);
                        }
                    },
                    'completed_at_formatted',
                ];
            case self::FIELDS_DEFAULT:
            default:
                return [
                    'id',
                    'location_id',
                    'location_name'      => function (Maintenance $model) {
                        return $model->location->name;
                    },
                    'location_address'   => function (Maintenance $model) {
                        return $model->location->address;
                    },
                    'location_latitude'  => function (Maintenance $model) {
                        return $model->location->latitude;
                    },
                    'location_longitude' => function (Maintenance $model) {
                        return $model->location->longitude;
                    },
                    'contract_code'      => function (Maintenance $model) {
                        return $model->equipment->contract_code;
                    },
                    'contract_material'  => function (Maintenance $model) {
                        return $model->equipment->material;
                    },
                    'equipment_id',
                    'equipment_name'     => function (Maintenance $model) {
                        return $model->equipment->name;
                    },
                    'equipment_code'     => function (Maintenance $model) {
                        return $model->equipment->code;
                    },
                    'equipment_details'     => function (Maintenance $model) {
                        return $model->equipment->details;
                    },
                    'equipment_floor'     => function (Maintenance $model) {
                        return $model->equipment->floor;
                    },
                    'equipment_zone'     => function (Maintenance $model) {
                        return $model->equipment->zone;
                    },
                    'equipment_place'     => function (Maintenance $model) {
                        return $model->equipment->place;
                    },
                    'equipment_quantity'     => function (Maintenance $model) {
                        return $model->equipment->quantity;
                    },
                    'report_url'         => function (Maintenance $model) {
                        if (!empty($model->report)) {
                            return $model->report->getFileUrl();
                        }
                    },
                    'status',
                    'status_label',
                    'note',
                    'completed_at_formatted',
                ];
        }
    }

    public function getStatusColor()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return "yellow";
            case self::STATUS_ASSIGNED:
                return "teal";
            case self::STATUS_START:
                return "blue";
            case self::STATUS_COMPLETE:
                return "green";
            case self::STATUS_NOT_COMPLETE:
                return "red";
            default:
                return "yellow";
        }
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($insert) {
            $this->log("Created the service");
        }
    }

    public function log($message, $username = null, $datetime = null)
    {
        MaintenanceLog::log($this->id, $message, $username, $datetime);
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::className(), ['maintenance_id' => 'id']);
    }

    public function calculateDuration()
    {
        /* @var $barcodesScans BarcodeScan[] */
        $barcodesScans = $this->getBarcodeScans()
            ->with(['maintenanceVisit'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        $totalTime = 0;
        $currentVisit = null;
        $previousTime = null;
        foreach ($barcodesScans as $index => $barcodesScan) {
            if ($barcodesScan->visit_id != $currentVisit) {
                $currentVisit = $barcodesScan->visit_id;
            } else {
                $totalTime += strtotime($barcodesScan->created_at) - strtotime($previousTime);
            }
            $previousTime = $barcodesScan->created_at;
        }
        $this->duration = ceil(abs($totalTime / 60));
        $this->save(false);
        return $totalTime;
    }


    public function regeneratePDFReport($destination = "F")
    {
        $mCode = $this->equipment->getMaintenanceFormCode();
        $maintenances = Maintenance::find()->where(['report_id' => $this->report_id])->all();

        $month = $this->month;

        $lookingForColumns = [];
        foreach ($maintenances as $index => $maintenance) {
            if (empty($lookingForColumns["{$month}{$maintenance->visit_number}"])) {
                $lookingForColumns["{$month}{$maintenance->visit_number}"] = [];
            }
            $lookingForColumns["{$month}{$maintenance->visit_number}"][] = "{$maintenance->equipment->code}:{$maintenance->visit_number}";
        }
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(Yii::getAlias("@static/maintenance/{$mCode}.xlsx"));
        $worksheet = $spreadsheet->getActiveSheet();
        $iterator = $worksheet->getRowIterator();
        $header = $iterator->current();
        $cellIterator = $header->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE);
        $columnCodes = [];
        foreach ($cellIterator as $cell) {
            /* @var $cell Cell */
            if (array_key_exists($cell->getValue(), $lookingForColumns)) {
                $columnCodes[$cell->getColumn()] = $lookingForColumns[$cell->getValue()];
            }
        }

        $results = [];
        $currentCategory = "";
        $currentSubCategory = "";
        $i = 1;
        for ($iterator->next(); $iterator->valid(); $iterator->next()) {
            $i++;
            try {
                $row = $iterator->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() == "A") {
                        $value = $cell->getValue();
                        if (!empty($value)) {
                            $currentCategory = "{$i}:" . $cell->getValue();
                            if (empty($results[$currentCategory])) {
                                $results[$currentCategory] = [];
                            }
                        }
                    } else if ($cell->getColumn() == "B") {
                        $value = $cell->getValue();
                        if (!empty($value)) {
                            $currentSubCategory = "{$i}:" . $value;
                            if (empty($results[$currentCategory][$currentSubCategory])) {
                                $results[$currentCategory][$currentSubCategory] = [];
                            }
                        } else {
                            break;
                        }
                    } else {
                        if (array_key_exists($cell->getColumn(), $columnCodes)) {
                            foreach ($columnCodes[$cell->getColumn()] as $equipmentCode) {
                                $results[$currentCategory][$currentSubCategory][$equipmentCode] = $cell->getValue();
                            }
                        }
                    }
                }
            } catch (Exception $exception) {
                continue;
            }
        }
        //we have $results;
//        echo "<pre>";
//        print_r($results);
//        echo "</pre>";
//        exit();
        $location = Location::find()
            ->where(['id' => $this->location_id])
            ->one();
        $technicianName = $this->technician->name;
        $customerName = $this->customer_name;
        $customerSignature = $this->customer_signature_path;
        $technicianSignature = $this->technician_signature_path;
        $notes = $this->note;
        $atlnotes = $this->atl_note;
        $reportId = $this->generatePdfReport($mCode, $results, $location, $this->report->month, $this->report->year, $customerName, $customerSignature, $technicianName, $technicianSignature, $notes, $atlnotes, $destination);
        if($destination == Pdf::DEST_BROWSER){
            return $reportId;
        }
    }


    /**
     * @param $form
     * @param $data
     * @param $location Location
     * @param $month
     * @param $year
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public function generatePdfReport($form, $data, $location, $month, $year, $customerName, $customerSignature, $technicianName, $technicianSignature, $notes, $atlnotes , $destination = "F")
    {
        $maintenanceReport = $this->report;
        $path = Yii::getAlias("@static/upload/maintenance_reports/{$maintenanceReport->year}/{$maintenanceReport->month}");
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $reportSize = Pdf::FORMAT_A4;
        switch ($form){
            case "E":
            case "F":
            case "C":
            case "D":
                $reportSize =  [148, 210];
        }

        $pdf = new Pdf([
            'mode'        => Pdf::MODE_BLANK,
            'format'      => $reportSize,//Pdf::FORMAT_A4, //[148, 210],//A5 => 148mm x 210mm
            'content'     => Yii::$app->controller->renderPartial("@admin/views/_maintenance_report", [
                'maintenance_id' => $this->id,

                'form'                => $form,
                'data'                => $data,
                'location'            => $location,
                'customerName'        => $customerName,
                'customerSignature'   => $customerSignature,
                'technicianName'      => $technicianName,
                'technicianSignature' => $technicianSignature,

                'year'      => $year,
                'month'     => $month,
                'notes'     => $notes,
                'atl_notes' => $atlnotes,

                'maintenance' => $this,
            ]),
            'options'     => [
                'title'        => \Yii::t("app", "Maintenance Report {id}-{location}-{form}/{year}-{month}", [
                    'location' => $location->code,
                    'year'     => $year,
                    'month'    => $month,
                    'form'     => $form,
                    'id'       => $this->id,
                ]),
                'fontDir'      => array_merge($fontDirs, [
                    Yii::getAlias("@static/fonts"),
                ]),
                'fontdata'     => [
                    'fontellocheck' => [
                        'R' => 'fontello-check.ttf',
                    ],
                    'tajawal'       => [
                        'R'      => 'almarai-v5-arabic-regular.ttf',
                        'B'      => 'almarai-v5-arabic-regular.ttf',
                        'useOTL' => 0xFF, 'useKashida' => 75,
                    ],
                ],
                'default_font' => 'helvetica'
            ],
            'methods'     => [
//                'SetFooter' => ['|{PAGENO}/{nb}|'],
            ],
            'cssInline'   => "
                    .ar{font-family: tajawal;}
                    .check{font-family: fontellocheck;}
                    .blue{color: rgb(55,150,255);}
                    .red{color: #ff3737;}
                    .brightred{color: #ff3737;}
                    .small{font-size: 2.9mm;}
                    .medium{font-size: 3.2mm;}
                    .large{font-size: 3.8mm;}
                    .fw500{font-weight: 500;}
                    .bold{font-weight: bold;}
                    .bolder{font-weight: 900;}
                    .right{text-align: right;}
                    .center{text-align: center;}
                    table.bordered{padding: 7px;border: 0.1mm solid #888;}
                    table.from-to-table td{font-size: 9px;}
                    .border-top{border-top: 0.1mm solid #888;}
                    .border-right{border-right: 0.0mm solid #888;}
                    .border-left{border-left: 0.0mm solid #888;}
                    .border-bottom{border-bottom: 0.1mm solid #888;padding: 1mm}
                    .border-bottom-dotted{border-bottom: 0.4mm dotted #888; padding: 1mm}
                    .border-all{border: 0.1mm solid #888;border-left: none;}
                    .border-all-l{border: 0.1mm solid #888;border-right: none;}
                    .border-v{border: 0.1mm solid #888;border-left: none;border-right: none;}
                    .padding-left{padding-left: 5px}
                    .padding-v{padding-top: 0.5mm;padding-bottom: 0.5mm;}
                    .all-border-bottom tr td{border-bottom: 0.1mm solid #888; padding-top:2mm}
                    .no-border-bottom tr td{border-bottom: none; padding-top:0mm}
                    .order-details{font-size: 8px;}
                    .order-details tr.title{font-size: 8px;}
                    .order-details tr.title{background-color: #fafafa;}
                    .order-details tr.title th{padding-top: 2px;border-bottom: 0.1mm solid #888;border-top: 0.1mm solid #888;}
                    .order-details tbody tr td{border-bottom: 0.1mm solid #888;}
                    .summary-1{margin-bottom:10px;margin-top:10px;font-size:9pt;padding-top:10px}
                    .summary-table .border-top{border-top: 0.1mm solid #888;}
                    .summary-table .border-bottom{border-bottom: 0.1mm solid #555;}
                ",
            'filename'    => $maintenanceReport->getFilePath(),
            'destination' => $destination,//'F',
//            'destination' => 'I',
        ]);
        $pdf->marginHeader = 0;
        $pdf->marginLeft = 7;
        $pdf->marginRight = 7;
        $pdf->marginTop = 7;
        $pdf->marginBottom = 7;

        $pdf->defaultFont = 'helvetica';
        $pdf->defaultFontSize = 4;

        $pdf->getApi()->AddFontDirectory(Yii::getAlias("@static/fonts"));

        if($destination == Pdf::DEST_BROWSER){
            return $pdf->render();
        }

        $pdf->render();


        //start save a tif version
        $tifPath = Yii::getAlias("@static/upload/maintenance_reports/{$maintenanceReport->year}/{$maintenanceReport->month}/{$maintenanceReport->id}_{$maintenanceReport->random_token}.tif");
        $im2 = new \Imagick();
        $im2->setResolution(100, 100);
        $im2->setCompressionQuality(50);
        $im2->setCompression(\Imagick::COMPRESSION_JPEG);
        $im2->readImage($maintenanceReport->getFilePath());
        $im2->setImageFormat("tiff");
        $im2->setImageColorSpace(\Imagick::COLORSPACE_RGB);
        $im2->writeImages($tifPath, true);
        //end save a tif version

        return $maintenanceReport->id;
        return $maintenanceReport->getFilePath();
    }

}
