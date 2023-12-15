<?php

namespace technician\modules\v1\controllers;

use common\components\extensions\api\ApiController;
use common\components\settings\Setting;
use common\data\Countries;
use common\models\Equipment;
use common\models\EquipmentMaintenanceBarcode;
use common\models\Location;
use common\models\Problem;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

/**
 * Description of DataController
 *
 * @author Tarek K. Ajaj
 */
class DataController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function actionCheckUpdate()
    {
        $this->isGet();
        $dotversion = \Yii::$app->getRequest()->get("version");
        $platform = \Yii::$app->getRequest()->get("platform");

        $version = $this->dotVertionToInt($dotversion);
        switch ($platform) {
            case "android":
                if ($version < $this->dotVertionToInt(Setting::getValue("technician_android_version"))) {
                    return [
                        'update'  => true,
                        'version' => Setting::getValue("technician_android_version"),
                        'link'    => Setting::getValue("technician_android_store"),
                    ];
                }
            case "ios":
                if ($version < $this->dotVertionToInt(Setting::getValue("technician_ios_version"))) {
                    return [
                        'update'  => true,
                        'version' => Setting::getValue("technician_android_version"),
                        'link'    => Setting::getValue("technician_ios_store"),
                    ];
                }
        }
        return [
            'update' => false,
        ];
    }

    private function dotVertionToInt($dotVersion = "1.0.0")
    {
        $versionParts = explode(".", $dotVersion);
        $version = (intval($versionParts[0]) * 10000) + (intval($versionParts[1]) * 100) + intval($versionParts[2]);
        return $version;
    }

    public function actionTimezones()
    {
        $this->isGet();
        return Countries::getTimeZonesList();
    }

    public function actionLocations($query)
    {
        if (strlen($query) >= 3) {
            $locations = Location::find()
                ->where([
                    'AND',
                    ['status' => Location::STATUS_ENABLED],
                    [
                        'OR',
                        ['LIKE', 'code', $query],
                        ['LIKE', 'name', $query]
                    ]
                ])->all();
            return ArrayHelper::getColumn($locations, function ($model) {
                return [
                    'id'   => $model->id,
                    'name' => "{$model->code} - {$model->name}"
                ];
            });
        }
        return [];
    }

    public function actionEquipments($location_id)
    {
        $equipments = Equipment::find()
            ->indexBy("id")
            ->where([
                'AND',
                ['status' => Equipment::STATUS_ENABLED],
                ['location_id' => $location_id]
            ])->all();
        return $equipments;
    }


    public function actionRequestForm($query)
    {
        $this->isGet();
        if (strlen($query) >= 3) {
            return [
                'locations' => Location::find()
                    ->where([
                        'AND',
                        ['status' => Location::STATUS_ENABLED],
                        [
                            'OR',
                            ['LIKE', 'code', $query],
                            ['LIKE', 'name', $query]
                        ]
                    ])
                    ->indexBy("id")
                    ->all(),
                'problems'  => Problem::findEnabled()->indexBy("id")->all()
            ];
        }
        return [
            'locations' => [],
            'problems'  => [],
        ];
    }


    public function actionLocationByBarcode($barcode)
    {
        $this->isGet();
        $maintenanceBarcode = EquipmentMaintenanceBarcode::find()->where(['barcode' => $barcode])->one();
        if (!empty($maintenanceBarcode)) {
            return [
                'locations'    => Location::find()
                    ->where([
                        'AND',
                        ['status' => Location::STATUS_ENABLED],
                        ['id' => $maintenanceBarcode->equipment->location_id]
                    ])
                    ->indexBy("id")
                    ->all(),
                'equipment_id' => $maintenanceBarcode->equipment_id,
                'problems'     => Problem::findEnabled()->indexBy("id")->all()
            ];
        }
        throw new ServerErrorHttpException("No equipment found for scanned barcode");
        return [
            'locations'    => [],
            'problems'     => [],
            'equipment_id' => null
        ];
    }

}
