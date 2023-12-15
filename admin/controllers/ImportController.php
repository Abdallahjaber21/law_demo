<?php


namespace admin\controllers;


use admin\models\BulkModelImportForm;
use admin\models\ExcelUploadForm;
use common\config\includes\P;
use common\models\Account;
use common\models\Category;
use common\models\Customer;
use common\models\Division;
use common\models\EngineOilTypes;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\EquipmentCategory;
use common\models\EquipmentCaValue;
use common\models\EquipmentMaintenanceBarcode;
use common\models\EquipmentType;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\MainSector;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Technician;
use common\models\UnitType;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class ImportController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => P::c(P::IMPORT_SECTION_SECTION_ENABLED),
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionMainSectors()
    {
        $this->view->title = "Main Sectors";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            "name" => "Name",
            "description" => "Description",
            "division_id" => "Division",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $model->import($columns_map, Yii::$app->request->post(), 'name', MainSector::className(), function ($row) {
                $row['division_id'] = Division::find()->where(['name' => $row['division_id']])->one()->id;
                $row['status'] = MainSector::STATUS_ENABLED;
                return $row;
            });
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionSectors()
    {
        $this->view->title = "Sectors";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            "code" => "code",
            //"name"                  => "name",
            "default_technician_id" => "technician",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $technicians = ArrayHelper::map(Technician::find()->all(), "code", function ($model) {
                return $model;
            });
            $model->import($columns_map, Yii::$app->request->post(), 'code', Sector::className(), function ($row) use ($technicians) {
                if (!empty($row['default_technician_id'])) {
                    if (array_key_exists($row['default_technician_id'], $technicians)) {
                        $row['default_technician_id'] = $technicians[$row['default_technician_id']]->id;
                    }
                }
                $row['name'] = $row['code'];
                $row['status'] = Sector::STATUS_ENABLED;
                return $row;
            });
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionCustomers()
    {
        $this->view->title = "Customers";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            "code" => "code",
            "name" => "name",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $model->import($columns_map, Yii::$app->request->post(), 'code', Customer::className(), function ($row) {
                $row['status'] = Customer::STATUS_ENABLED;
                $row['name'] = ucwords($row['name']);
                return $row;
            });
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionLocations()
    {
        $this->view->title = "Locations";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            //model=>file
            "name" => "name",
            "code" => "code",
            "division_id" => "division",
            "sector_id" => "sector",
            "segment_path_id" => "segment path",
            "address" => "address",
            "coordinates" => "coordinates",
            "owner" => "tenant",
            "owner_phone" => "tenant phone",
            "expiry_date" => "Contract Expiry Date",
        ];
        $errors = [];
        $row_nb = 1;
        if (Yii::$app->getRequest()->isPost) {
            $result = $model->import($columns_map, Yii::$app->request->post(), 'code', Location::className(), function ($row) use (&$row_nb, &$errors) {

                $row_nb++;

                $division = Division::find()->where(['name' => $row['division_id']])->one();
                $sector = Sector::find()->where(['name' => $row['sector_id']])->one();
                $segment_path = SegmentPath::find()->where(['code' => $row['segment_path_id']])->one();

                if (!empty($division) && !empty($sector) && !empty($segment_path) && !empty($row['name']) && !empty($row['code'])) {

                    $coordinates = explode(',', $row['coordinates']);

                    $row['division_id'] = $division->id;
                    $row['sector_id'] = $sector->id;
                    $row['segment_path_id'] = $segment_path->id;

                    if (!empty($coordinates[0])) {
                        $row['latitude'] = trim(str_replace('"', '', trim($coordinates[0])));
                    }
                    if (!empty($coordinates[1])) {

                        $row['longitude'] = trim(str_replace('"', '', trim($coordinates[1])));
                    }
                    $row['status'] = Location::STATUS_ENABLED;
                    $row['name'] = ucwords($row['name']);

                    if (!empty($row['expiry_date'])) {
                        $row['expiry_date'] = $row['expiry_date'] . ' 00:00:00';
                    }

                    unset($row['coordinates']);
                    return $row;
                } else {
                    $errors[] = $row_nb;
                    return null;
                }
            });

            if (count($errors) > 0)
                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", 'Wrong Data Provided To The Following Rows: ' . implode(',', $errors)));

            if ($result) {
                return $this->render('import', $result);
            }
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'template_path' => 'locations'
        ]);
    }

    public function actionEquipments()
    {
        $this->view->title = "Equipments";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            //model=>file
            "name" => "name",
            "code" => "code",
            "category_id" => "category",
            "equipment_type_id" => "equipment type",
            "division_id" => "division",
        ];
        $errors = [];
        $row_nb = 1;
        if (Yii::$app->getRequest()->isPost) {
            $result = $model->import($columns_map, Yii::$app->request->post(), 'code', Equipment::className(), function ($row) use (&$row_nb, &$errors) {

                $row_nb++;

                $division = Division::find()->where(['name' => $row['division_id']])->one();
                $category = Category::find()->where(['code' => $row['category_id']])->one();

                $equipment_type = EquipmentType::find()->where(['code' => $row['equipment_type_id']])->one();

                if (!empty($division) && !empty($category) && !empty($equipment_type) && !empty($row['name']) && !empty($row['code'])) {
                    $row['division_id'] = $division->id;
                    $row['category_id'] = $category->id;
                    $row['equipment_type_id'] = $equipment_type->id;
                    $row['status'] = Equipment::STATUS_ENABLED;
                    $row['name'] = ucwords($row['name']);

                    return $row;
                } else {
                    $errors[] = $row_nb;

                    return null;
                }
            });

            if (count($errors) > 0)
                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", 'Wrong Data Provided To The Following Rows: ' . implode(',', $errors)));

            if ($result) {
                return $this->render('import', $result);
            }
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'template_path' => 'equipments'
        ]);
    }

    public function actionCategories()
    {
        $this->view->title = "Categories";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            //model=>file
            "name" => "name",
            "parent_id" => "parent",
            "code" => "code",
        ];

        $errors = [];
        $row_nb = 1;

        if (Yii::$app->getRequest()->isPost) {
            $result = $model->import($columns_map, Yii::$app->request->post(), 'code', Category::className(), function ($row) use (&$row_nb, &$errors) {

                $row_nb++;

                if (!empty($row['name']) && !empty($row['code'])) {

                    $row['parent_id'] = @Category::find()->where(['name' => $row['parent_id']])->one()->id;
                    $row['status'] = Category::STATUS_ENABLED;
                    $row['name'] = strtoupper($row['name']);

                    return $row;
                } else {
                    $errors[] = $row_nb;
                    return null;
                }
            });

            if (count($errors) > 0)
                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", 'Wrong Data Provided To The Following Rows: ' . implode(',', $errors)));

            if ($result) {
                return $this->render('import', $result);
            }
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'template_path' => 'categories'
        ]);
    }

    public function actionEquipmentTypes()
    {
        $this->view->title = "Equipment Types";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            //model=>file
            "name" => "name",
            "category_id" => "category",
            "code" => "code",

        ];

        $errors = [];
        $row_nb = 1;

        if (Yii::$app->getRequest()->isPost) {
            $result = $model->import($columns_map, Yii::$app->request->post(), 'code', EquipmentType::className(), function ($row) use (&$row_nb, &$errors) {

                $row_nb++;

                $category = Category::find()->where(['code' => $row['category_id']])->one();

                if (!empty($row['name']) && !empty($row['code']) && !empty($category)) {
                    $row['name'] = ucfirst($row['name']);
                    $row['category_id'] = $category->id;
                    $row['code'] = $row['code'];
                    $row['status'] = EquipmentType::STATUS_ENABLED;


                    return $row;
                } else {
                    $errors[] = $row_nb;
                    return null;
                }
            });

            if (count($errors) > 0)
                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", 'Wrong Data Provided To The Following Rows: ' . implode(',', $errors)));

            if ($result) {
                return $this->render('import', $result);
            }
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'template_path' => 'equipment-types'
        ]);
    }

    public function actionBulkEquipments()
    {
        $this->view->title = "Equipments for multiple locations";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $locationsExcel = new ExcelUploadForm();
        $columns_map = [
            //model=>file
            "code" => "code",
            //"location_id"    => "location",
            //"name"           => "name",
            "equipment_type" => "type",
            "contract_code" => "contract",
            //"manufacturer"   => "manufacturer",
            "unit_type" => "category",
            //"temporary_in"   => "temporary in",
            //"temporary_out"  => "temporary out",
            "material" => "material",
            "floor" => "floor",
            "zone" => "zone",
            "place" => "place",
            "details" => "details",
            "quantity" => "quantity",
            "expire_at" => "expiry date",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $data = $locationsExcel->fromPost(Yii::$app->request->post());
            //            echo "<pre>";
            //            print_r($data);
            //            exit();
            //            $locations_codes = Yii::$app->request->post('locations_codes');
            $locationsCodes = ArrayHelper::getColumn($data, "location", false); //explode(",", $locations_codes);

            $locations = ArrayHelper::map(Location::find()->where(['code' => $locationsCodes])->all(), "code", function ($model) {
                return $model;
            });
            $result = $model->import($columns_map, Yii::$app->request->post(), 'code', Equipment::className(), function ($row) use ($locations) {
                $locationCode = $row['location_id'];
                if (!empty($row['location_id'])) {
                    if (array_key_exists($row['location_id'], $locations)) {
                        $row['location_id'] = $locations[$row['location_id']]->id;
                    } else {
                        $row['location_id'] = null;
                    }
                }

                if (!empty($row['equipment_type'])) {
                    $row['name'] = (new Equipment(['equipment_type' => $row['equipment_type']]))->equipment_type_label; // . ' ' . $row['code'];
                    //                        (str_replace($locationCode, '', $row['code']));
                    //                        (substr($row['code'], -1));
                }

                //if (!empty($row['manufacturer'])) {
                $row['manufacturer'] = '001'; //($row['manufacturer'] === "001" ? '001' : '002');
                //}
                $row['status'] = Equipment::STATUS_ENABLED;

                //$row['temporary_in'] = strtolower($row['temporary_in']) === 'yes';
                //$row['temporary_out'] = strtolower($row['temporary_out']) === 'yes';

                $dateTime = DateTime::createFromFormat('d.m.Y', $row['expire_at']);
                $row['expire_at'] = $dateTime->format('Y-m-d');
                $row['name'] = ucwords($row['name']);

                return $row;
            }, true, array_keys($locations), 'location_id');
            if ($result) {
                return $this->render('import', $result + [
                    'fields' => [['model' => $locationsExcel, 'attribute' => 'file', 'options' => ['accept' => 'xls,xlsx'], 'label' => 'Locations Codes']],
                    'hint' => 'Attach an excel file with only list of locations codes to import equipments to'
                ]);
            }
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'fields' => [['model' => $locationsExcel, 'attribute' => 'file', 'options' => ['accept' => 'xls,xlsx'], 'label' => 'Locations Codes']],
            'hint' => 'Attach an excel file with only list of locations codes to import equipments to'
        ]);
    }

    public function actionLocationEquipments()
    {
        $this->view->title = "Location Equipments";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            //model=>file
            "id" => 'id',
            "location_id" => "location",
            "equipment_id" => "equipment",
            "code" => "code",
        ];
        $errors = [];

        $template_path = (!empty(Account::getAdminDivisionModel() ? "location-equipments-" . strtolower(Account::getAdminDivisionModel()->name) : null));

        if (Yii::$app->getRequest()->isPost) {


            $selected_division = Yii::$app->getRequest()->getBodyParam('division_id');

            switch ($selected_division) {
                case Division::DIVISION_PLANT:

                    $template_path = "locations-equipments-plant";

                    $columns_map += [
                        "status" => "status",
                        "remarks" => "remarks",
                        "path_1" => "Location",
                        "path_2" => "Division",
                        "custom_attrs_1" => "Plate no",
                        "custom_attrs_2" => "Item Brand",
                        "custom_attrs_3" => "Model",
                        "custom_attrs_4" => "Item Owner",
                        "driver_id" => "Driver",
                        "chassie_number" => "Chassie Number",
                        "motor_fuel_type" => "Motor Fuel Type",
                        "meter_value" => "Meter Value",
                        "meter_damaged" => "Meter Status"
                    ];
                    break;
                case Division::DIVISION_MALL:

                    $template_path = "locations-equipments-mall";

                    $columns_map += [
                        "path_1" => "Floor",
                        "path_2" => "Zone",
                        "path_3" => "Unit No",
                        "path_4" => "Location",
                    ];
                    break;
                case Division::DIVISION_VILLA:

                    $template_path = "locations-equipments-villa";

                    $columns_map += [
                        "path_1" => "equipment path",
                    ];
                    break;
            }

            $row_nb = 1;

            $result = $model->importNew($columns_map, Yii::$app->request->post(), 'id', LocationEquipments::className(), function ($row) use (&$selected_division, &$row_nb, &$errors) {

                $row_nb++;

                $location = Location::find()->where(['code' => trim($row['location_id'])])->one();
                $equipment = Equipment::find()->where(['name' => trim($row['equipment_id'])])->one();
                if (!empty($row['driver_id'])) {
                    $driver = Technician::find()->where(['badge_number' => trim($row['driver_id'])])->one();
                }
                $motor_fuel_types = (new EngineOilTypes())->motor_fuel_type_id_list;

                if (!empty($location) && !empty($equipment) && !empty($row['code'])) {

                    switch ($selected_division) {
                        case Division::DIVISION_PLANT:

                            $row['status'] = array_search(trim(strtolower($row['status'])), (new Equipment())->status_list);
                            $row['location_id'] = $location->id;
                            $row['equipment_id'] = $equipment->id;
                            $row['division_id'] = $selected_division;
                            $row['driver_id'] = @$driver->id;
                            $row['motor_fuel_type'] = array_search($row['motor_fuel_type'], $motor_fuel_types); // 10 - 20

                            // Create segment path value 
                            $segment_path_array = [
                                "Location" => $row['path_1'],
                                "Division" => $row['path_2'],
                            ];

                            $row['value'] = Equipment::GetJsonSegmentValue($segment_path_array, ',');

                            break;
                        case Division::DIVISION_MALL:

                            $row['status'] = Equipment::STATUS_ENABLED;
                            $row['location_id'] = $location->id;
                            $row['equipment_id'] = $equipment->id;
                            $row['division_id'] = $selected_division;

                            $segment_path_array = [
                                "Floor" => $row['path_1'],
                                "Zone" => $row['path_2'],
                                "Unit No" => $row['path_3'],
                                "Location" => $row['path_4'],
                            ];

                            $row['value'] = Equipment::GetJsonSegmentValue($segment_path_array, ',');

                            break;
                        case Division::DIVISION_VILLA:

                            $equipment_path_arr = explode(",", $row['path_1']);

                            foreach ($equipment_path_arr as $path) {

                                $model = LocationEquipments::find()->where(['id' => $row['id']])->one();

                                if (empty($model)) {
                                    $model = new LocationEquipments();
                                }

                                $model->location_id = $location->id;
                                $model->code = $row['code'];
                                $model->equipment_id = $equipment->id;
                                $model->division_id = $selected_division;
                                $model->driver_id = @$driver->name;
                                $model->status = Equipment::STATUS_ENABLED;

                                $segment_path_array = [
                                    "Path" => $path,
                                ];

                                $model->value = Equipment::GetJsonSegmentValue($segment_path_array, ',');

                                if (!$model->save()) {
                                    print_r($model->errors);
                                    exit;
                                }
                            }

                            return -1; // do not insert current row insted do what in the loop
                            break;
                    }

                    return $row;
                } else {
                    $errors[] = $row_nb;
                    return null;
                }
            }, true, [], false, function ($saved_model, $row) use (&$selected_division) {

                if ($selected_division == Division::DIVISION_PLANT) {

                    EquipmentCaValue::deleteAll(['location_equipment_id' => $saved_model->id]);

                    $equipment = Equipment::find()->where(['name' => trim($row['equipment_id'])])->one();

                    $custom_attributes_array = [
                        "Plate no" => $row['custom_attrs_1'],
                        "Item Brand" => $row['custom_attrs_2'],
                        "Model" => $row['custom_attrs_3'],
                        "Item Owner" => $row['custom_attrs_4'],
                    ];

                    foreach ($custom_attributes_array as $index => $datatum) {
                        $custom_attrs_model = new EquipmentCaValue();
                        $custom_attrs_model->equipment_ca_id = EquipmentCa::find()->where(['name' => $index])->one()->id;
                        $custom_attrs_model->equipment_id = $equipment->id;
                        $custom_attrs_model->location_equipment_id = $saved_model->id;
                        $custom_attrs_model->value = $datatum . '';
                        if (!$custom_attrs_model->save()) {
                            print_r($custom_attrs_model->errors);
                            exit;
                        }
                    }
                }

                return true;
            });

            if (count($errors) > 0)
                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", 'Wrong Data Provided To The Following Rows: ' . implode(',', $errors)));

            if ($result) {
                // return $this->render('import', $result);
                return $this->render('import', array_merge($result, ['require_division' => true]));
            }
        }

        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'require_division' => true,
            'template_path' => $template_path
        ]);
    }

    public function actionMaintenanceTasks()
    {
        $this->view->title = "Maintenance Tasks QR Codes";
        $model = new BulkModelImportForm(['override_existing' => false]);
        $columns_map = [
            //model=>file
            'barcode' => 'barcode',
            'location' => 'location',
            'equipment_id' => 'equipment',
            'code' => 'code'
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $equipments = ArrayHelper::map(Equipment::find()->select(['code', 'id'])->all(), "code", "id");
            $result = $model->import($columns_map, Yii::$app->request->post(), 'barcode', EquipmentMaintenanceBarcode::className(), function ($row) use ($equipments) {
                if (!empty($row['equipment_id'])) {
                    $row['equipment_id'] = trim($row['equipment_id']);
                    if (array_key_exists($row['equipment_id'], $equipments)) {
                        $row['equipment_id'] = $equipments[$row['equipment_id']];
                        //EquipmentMaintenanceBarcode::deleteAll(['equipment_id'=> $row['equipment_id']]);
                    } else {
                        $row['equipment_id'] = null;
                    }
                }
                $row['status'] = Equipment::STATUS_ENABLED;

                return $row;
            }, true);
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionEquipmentsCategories()
    {
        $this->view->title = "Equipment Categories";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            "key" => "category",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $model->import($columns_map, Yii::$app->request->post(), 'key', EquipmentCategory::className(), function ($row) {
                $row['name'] = strtoupper($row['key']);
                $row['key'] = strtoupper($row['key']);
                return $row;
            });
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'hint' => null,
            'fields' => null
        ]);
    }
    public function actionEquipmentsTypes()
    {
        $this->view->title = "Equipment Types";
        $model = new BulkModelImportForm(['override_existing' => true]);
        $columns_map = [
            "key" => "type",
        ];
        $errors = [];
        if (Yii::$app->getRequest()->isPost) {
            $model->import($columns_map, Yii::$app->request->post(), 'key', EquipmentType::className(), function ($row) {
                $row['name'] = strtoupper($row['key']);
                $row['key'] = strtoupper($row['key']);
                return $row;
            });
        }
        return $this->render('import', [
            'model' => $model,
            'errors' => $errors,
            'hint' => null,
            'fields' => null
        ]);
    }
}
