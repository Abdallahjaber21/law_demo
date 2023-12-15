<?php

namespace admin\controllers;

use common\components\helpers\PhoneHelper;
use common\components\notification\widgets\AdminNotificationsPanelWidget;
use common\models\Account;
use common\models\AdminNotifications;
use common\models\Assignee;
use common\models\Category;
use common\models\City;
use common\models\Customer;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\Location;
use common\models\UserGrid;
use common\models\MainSector;
use common\models\Sector;
use common\models\AccountType;
use common\models\SegmentPath;
use common\models\State;
use common\models\Technician;
use common\models\User;
use common\models\users\Admin;
use common\models\EquipmentType;
use common\models\LocationEquipments;
use common\models\Profession;
use common\models\RepairRequest;
use common\models\Shift;
use common\models\TechnicianShift;
use DateTime;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Description of DependencyController
 *
 * @author Tarek K. Ajaj
 */
class DependencyController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionSearchEquipment($q)
    {
        $equipments = Equipment::find()
            ->joinWith(['location'])
            ->where([
                'OR',
                ["like", Equipment::tableName() . '.code', $q],
                ["like", Location::tableName() . '.name', $q],
                ["like", Location::tableName() . '.code', $q],
            ])
            ->andFilterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
            ->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        //        return $customers;
        return [
            'results' => ArrayHelper::getColumn($equipments, function (Equipment $equipment) {
                $desc = "";
                if ($equipment->temporary_in) {
                    $desc = "Temporary IN - ";
                }
                if ($equipment->temporary_out) {
                    $desc = "Temporary OUT - ";
                }
                $expire = \Yii::$app->formatter->asDate($equipment->expire_at);
                return [
                    'id' => $equipment->id,
                    'text' => "{$equipment->code} - {$equipment->name} - {$equipment->location->name} - 
                ({$desc}{$equipment->material} - Expires {$expire})"
                ];
            })
        ];
    }

    public function actionSearchUsers($q)
    {
        $users = User::find()
            ->where(['like', 'name', $q])
            ->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        //        return $customers;
        return [
            'results' => ArrayHelper::getColumn($users, function ($user) {
                return [
                    'id' => $user->id,
                    'text' => "{$user->name}"
                ];
            })
        ];
    }
    public function actionSearchDivisionType()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $type_id = $parents[0];
                if (!empty($type_id)) {
                    $type = AccountType::findOne($type_id);
                    $division = Division::find()->where(['id' => $type->division_id])->orderBy(['name' => SORT_ASC])->all();
                    $out = ArrayHelper::getColumn(
                        $division,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );
                    if (!empty($out[0])) {
                        return [
                            'output' => $out,
                            'selected' => $out[0]
                        ];
                    } else {
                        return [
                            'output' => $out
                        ];
                    }
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    public function actionSearchDivisionMainSectors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $division_id = $parents[0];
                if (!empty($division_id)) {
                    $main_Sectors = Division::findOne($division_id)->availableMainSectors;
                    $out = ArrayHelper::getColumn(
                        $main_Sectors,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function actionSearchDivisionSectors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $division_id = $parents[0];
                if (!empty($division_id)) {
                    $sectors = Division::getSectors($division_id);
                    $out = ArrayHelper::getColumn(
                        $sectors,
                        function ($model) {
                            return ['id' => $model->id, 'name' => implode('-', array_filter([$model->code, $model->name, $model->mainSector->division->name]))];
                        },
                        false
                    );

                    if (count($out) == 1) {
                        return ['output' => $out, 'selected' => $out];
                    }

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function actionSearchSectorsPaths()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [''];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $parents[0];
                if (!empty($sector_id)) {
                    $segment_paths = Sector::findOne($sector_id)->segmentPaths;
                    $out = ArrayHelper::getColumn(
                        $segment_paths,
                        function ($model) {
                            $segment_value = SegmentPath::getLayersValue($model->value);
                            return ['id' => $model->id, 'name' => "{$model->name} - {$segment_value}", 'data-path' => $segment_value];
                        },
                        false
                    );
                    if (!empty($out) && count($out) == 1) {
                        $selected = $out[0];
                    } else {

                        $selected = '';
                    }



                    return ['output' => $out, 'selected' => $selected];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function actionSearchEquipmentTypes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $equipment_category = $parents[0];
                if (!empty($equipment_category)) {
                    $equipment_types = EquipmentType::find()->where(['category_id' => $equipment_category])->all();
                    $out = ArrayHelper::getColumn(
                        $equipment_types,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionGetSegmentPath()
    {
        if ($this->request->post()) {
            $path_id = $this->request->post()['path_id'];

            $segment = SegmentPath::findOne($path_id)->value;

            Yii::$app->response->format = Response::FORMAT_JSON;

            return Json::decode($segment);
        }
    }

    public function actionSearchDivisionEquipmentCa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $division_id = $parents[0];
                if (!empty($division_id)) {
                    $equipmentca = EquipmentCa::find()->where(['division_id' => $division_id])->all();

                    $out = ArrayHelper::getColumn(
                        $equipmentca,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchEquipmentEquipmentCa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $equipment_id = $parents[0];

                if (!empty($equipment_id)) {
                    $equipment = Equipment::find()->where(['id' => $equipment_id])->one();
                    if (!empty($equipment)) {

                        $division_id = $equipment->division_id;
                        print_r($division_id);
                        exit;
                        $out = ArrayHelper::getColumn(
                            $division_id,
                            function ($model) {
                                return ['id' => $model->id, 'name' => "{$model->name}"];
                            },
                            false
                        );

                        return ['output' => $out, 'selected' => ''];
                    }
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    public function actionSearchDivisionLocations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $division_id = $parents[0];
                if (!empty($division_id)) {
                    $locations = Location::find()->where(['division_id' => $division_id])->all();
                    $out = ArrayHelper::getColumn(
                        $locations,
                        function ($model) {
                            return ['id' => $model->id, 'name' => implode('-', array_filter([$model->code, $model->name]))];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchSectorsLocations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $parents[0];
                if (!empty($sector_id)) {
                    $locations = Sector::findOne($sector_id)->locations;
                    $out = ArrayHelper::getColumn(
                        $locations,
                        function ($model) {
                            return ['id' => $model->id, 'name' => implode('-', array_filter([$model->code, $model->name]))];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    // public function actionSearchEquipmentLocationsOld()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $out = [];
    //     if (isset($_POST['depdrop_sparents'])) {
    //         $parents = $_POST['depdrop_parents'];
    //         if ($parents != null) {
    //             $equipment_code = $parents[0];
    //             $division_id = $parents[1];

    //             if (!empty($division_id)) // get all division locations
    //             {
    //                 $locations = Location::find()->where(['division_id' => $division_id])->all();

    //                 if (!empty($locations)) {
    //                     $out = ArrayHelper::getColumn(
    //                         $locations,
    //                         function ($model) {
    //                             return ['id' => $model->code, 'name' => $model->code . ' - ' . $model->name];
    //                         },
    //                         false
    //                     );

    //                     if (count($out) == 1) {
    //                         return ['output' => $out, 'selected' => $out];
    //                     }

    //                     return ['output' => $out, 'selected' => ''];
    //                 }
    //             }

    //             // if (!empty($equipment_code)) {

    //             //     $user_division = Account::getAdminDivisionID();

    //             //     $locations_equipments =  LocationEquipments::find()->where(['=', 'code', $equipment_code]);

    //             //     if (!empty($user_division)) {
    //             //         $locations_equipments = $locations_equipments->andWhere(['division_id' => $user_division]);
    //             //     }

    //             //     $locations_equipments = $locations_equipments->asArray()->all();

    //             //     $locations = array_unique(ArrayHelper::getColumn($locations_equipments, 'location_id'));

    //             //     $out_locations = [];

    //             //     if (!empty($locations)) {
    //             //         foreach ($locations as $loc) {

    //             //             $location_model = Location::findOne($loc);

    //             //             $out_locations[] = [
    //             //                 'id' => $location_model->code,
    //             //                 'value' => $location_model->code . ' - ' . $location_model->name
    //             //             ];
    //             //         }
    //             //     }

    //             //     $out = ArrayHelper::getColumn(
    //             //         $out_locations,
    //             //         function ($model) {
    //             //             return ['id' => $model['id'], 'name' => $model['value']];
    //             //         },
    //             //         false
    //             //     );

    //             //     return ['output' => $out, 'selected' => ''];
    //             // }
    //         }
    //     }
    //     return ['output' => '', 'selected' => ''];
    // }
    public function actionSearchEquipmentLocations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $_POST['depdrop_all_params']['sector_dropdown'];

                if (!empty($sector_id)) // get all division locations
                {
                    $sector = Sector::findOne($sector_id);

                    $locations = $sector->locations;

                    if (!empty($locations)) {
                        $out = ArrayHelper::getColumn(
                            $locations,
                            function ($model) {
                                return ['id' => $model->code, 'name' => $model->code . ' - ' . $model->name];
                            },
                            false
                        );

                        if (count($out) == 1) {
                            return ['output' => $out, 'selected' => $out];
                        }

                        return ['output' => $out, 'selected' => ''];
                    }
                }

                // if (!empty($equipment_code)) {

                //     $user_division = Account::getAdminDivisionID();

                //     $locations_equipments =  LocationEquipments::find()->where(['=', 'code', $equipment_code]);

                //     if (!empty($user_division)) {
                //         $locations_equipments = $locations_equipments->andWhere(['division_id' => $user_division]);
                //     }

                //     $locations_equipments = $locations_equipments->asArray()->all();

                //     $locations = array_unique(ArrayHelper::getColumn($locations_equipments, 'location_id'));

                //     $out_locations = [];

                //     if (!empty($locations)) {
                //         foreach ($locations as $loc) {

                //             $location_model = Location::findOne($loc);

                //             $out_locations[] = [
                //                 'id' => $location_model->code,
                //                 'value' => $location_model->code . ' - ' . $location_model->name
                //             ];
                //         }
                //     }

                //     $out = ArrayHelper::getColumn(
                //         $out_locations,
                //         function ($model) {
                //             return ['id' => $model['id'], 'name' => $model['value']];
                //         },
                //         false
                //     );

                //     return ['output' => $out, 'selected' => ''];
                // }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchLocationEquipmentsText($order_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $location_code = $_POST['depdrop_all_params']['location-input-dropdown'];
                $category_id = $_POST['depdrop_all_params']['category_dropdown_id'];

                if (!empty($order_id)) {
                    $equipments = @RepairRequest::findOne($order_id)->equipment;

                    if (!empty($equipments)) {
                        $equipments = [$equipments];
                        $out = ArrayHelper::getColumn(
                            $equipments,
                            function ($model) {
                                $attributes = @Equipment::getEquipmentCustomAttributes($model->equipment->id, $model->id, "<br />");

                                return ['id' => $model->code, 'name' => $model->equipment->name . ' | ' . $model->code . ' | ' . $model->equipment->category->name . "<br />" . Equipment::getLayersValue($model->value, '') . $attributes];
                            },
                            false
                        );

                        return ['output' => $out, 'selected' => '', 'division' => !empty($location) ? Division::findOne($location->division_id)->name : '', 'Main_Sector' => !empty($location) ? $location->sector->mainSector->name : ''];
                    }
                }

                if (!empty($category_id) && !empty($location_code)) {

                    $equipments = [];

                    $user_division = Yii::$app->user->identity->division_id;

                    $location = Location::find()->where(['=', 'code', $location_code]);

                    if (!empty($user_division)) {
                        $location = $location->andWhere(['division_id' => $user_division]);
                    }

                    $location = $location->one();


                    $equipments = @LocationEquipments::find()->joinWith('equipment')->where(['location_id' => $location->id]);

                    if (!empty($category_id)) {
                        $equipments = $equipments->andWhere(['equipment.category_id' => $category_id]);
                    }

                    $equipments = $equipments->all();

                    if (!empty($location)) {
                        $out = ArrayHelper::getColumn(
                            $equipments,
                            function ($model) {
                                $attributes = @Equipment::getEquipmentCustomAttributes($model->equipment->id, $model->id, "<br />");

                                return ['id' => $model->code, 'name' => $model->equipment->name . ' | ' . $model->code . ' | ' . $model->equipment->category->name . "<br />" . Equipment::getLayersValue($model->value, '') . $attributes];
                            },
                            false
                        );
                    }

                    return ['output' => $out, 'selected' => '', 'division' => !empty($location) ? Division::findOne($location->division_id)->name : '', 'Main_Sector' => !empty($location) ? $location->sector->mainSector->name : ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchLocationsEquipments()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $location_id = $parents[0];
                if (!empty($location_id)) {
                    $locations = Location::findOne($location_id)->equipments;
                    $out = ArrayHelper::getColumn(
                        $locations,
                        function ($model) {
                            return ['id' => $model->id, 'name' => implode('-', array_filter([$model->code, $model->name]))];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchCountryStates()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $country_id = $parents[0];
                if (!empty($country_id)) {
                    $equipments = State::find()->orderBy(['name' => SORT_ASC])->where(['country_id' => $country_id])->all();
                    $out = ArrayHelper::getColumn(
                        $equipments,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchCategoryType()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $category_id = $parents[0];
                if (!empty($category_id)) {
                    $equipmenttype = EquipmentType::find()->orderBy(['name' => SORT_ASC])->where(['category_id' => $category_id])->all();
                    $out = ArrayHelper::getColumn(
                        $equipmenttype,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    public function actionSearchStateCities()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $state_id = $parents[0];
                if (!empty($state_id)) {
                    $equipments = City::find()->orderBy(['name' => SORT_ASC])->where(['state_id' => $state_id])->all();
                    $out = ArrayHelper::getColumn(
                        $equipments,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    public function actionSearchMainDivision()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $division_id = $parents[0];
                if (!empty($division_id)) {
                    $mainsectors = MainSector::find()->where(['division_id' => $division_id])->andWhere(['status' => MainSector::STATUS_ENABLED])->orderBy(['name' => SORT_ASC])->all();
                    $out = ArrayHelper::getColumn(
                        $mainsectors,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchSectorCountries()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $parents[0];
                if (!empty($sector_id)) {
                    $country = Sector::findOne($sector_id)->country;
                    $out = ArrayHelper::getColumn(
                        $country,
                        function ($model) {

                            if (!empty($model))
                                return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSearchSectorStates()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $parents[0];
                if (!empty($sector_id)) {
                    $state = Sector::findOne($sector_id)->state;

                    $out = ArrayHelper::getColumn(
                        $state,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );

                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function actionSearchSectorCities()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sector_id = $parents[0];
                if (!empty($sector_id)) {
                    $city = Sector::findOne($sector_id)->city;
                    $out = ArrayHelper::getColumn(
                        $city,
                        function ($model) {
                            return ['id' => $model->id, 'name' => "{$model->name}"];
                        },
                        false
                    );
                    return ['output' => $out, 'selected' => ''];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    public function actionSearchTeam($q)
    {
        /* @var $technicians Technician[] */
        $technicians = Technician::find()
            ->where([
                'OR',
                ["like", 'name', $q],
                // ["like", 'code', $q],
            ])
            ->all();
        /* @var $admins Admin[] */
        $admins = Admin::find()
            ->where([
                'OR',
                ["like", 'name', $q],
                // ["like", 'code', $q],
            ])
            ->all();
        /* @var $customers Customer[] */
        // $customers = Customer::find()
        //     ->where([
        //         'OR',
        //         ["like", 'name', $q],
        //         ['code' => $q],
        //     ])
        //     ->limit(20)
        //     ->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        //        return $customers;
        return [
            'results' => array_merge(
                ArrayHelper::getColumn($technicians, function (Technician $technician) {
                    return [
                        'id' => $technician->name,
                        //'text'  => "{$technician->code} - {$technician->name}",
                        'text' => $technician->name,
                        'name' => $technician->name,
                        'phone' => $technician->phone_number,
                        'type' => 'technician'
                    ];
                }),
                ArrayHelper::getColumn($admins, function (Admin $admin) {
                    return [
                        'id' => $admin->name,
                        'text' => $admin->name,
                        'name' => $admin->name,
                        'phone' => $admin->phone_number,
                        'type' => 'admin'

                    ];
                })
                // , ArrayHelper::getColumn($customers, function (Customer $customer) {
                //     return [
                //         'id'    => $customer->name,
                //         'text'  => $customer->name,
                //         'name'  => $customer->name,
                //         'phone' => PhoneHelper::formatToInternational($customer->phone, true)
                //     ];
                // })
            )
        ];
    }

    public function actionTeamLeader($order_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parents = $_POST['depdrop_all_params']['technician-id-input'];
            if ($parents != null) {
                $tech_ids = $parents;

                $technicians = Technician::find()->where(['id' => $tech_ids])->all();

                $out = ArrayHelper::getColumn(
                    $technicians,
                    function ($model) {
                        return ['id' => $model->id, 'name' => "{$model->badge_number} - {$model->name}"];
                    },
                    false
                );

                if (!empty($order_id)) {
                    $order = RepairRequest::findOne($order_id);

                    if (!empty($order->team_leader_id)) {
                        return ['output' => $out, 'selected' => $order->team_leader_id];
                    }
                }

                if (count($out) == 1) {
                    return ['output' => $out, 'selected' => $out];
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }



    // public function actionSearchSupervisorTechniciansOld($type = null, $order_id = null)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $out = [];
    //     if (isset($_POST['depdrop_all_params'])) {
    //         $location_code = $_POST['depdrop_all_params']['location_id_inputbox'];

    //         if (empty($location_code)) {
    //             $location_code = $_POST['depdrop_all_params']['location-input-dropdown'];
    //         }

    //         $datetime_input = $_POST['depdrop_all_params']['datetime_picker_input'];

    //         $technicians_supervisors = null;
    //         $division = null;

    //         if (!empty($location_code)) { // division + main_sector + account_type ( super - tech )

    //             $location = Location::find()->where(['code' => $location_code])->one();
    //             $division = @$location->division;
    //             $main_sector_id = @$location->sector->main_sector_id;

    //             $technicians_supervisors = Technician::find()->joinWith('account')->innerJoin('account_type', 'account_type.id = account.type')->select(['technician.id', 'technician.name', 'technician.division_id', 'technician.main_sector_id', 'technician.profession_id', 'account_type.name as type'])->where(
    //                 [
    //                     'AND',
    //                     ['technician.division_id' => $division->id],
    //                     ['technician.main_sector_id' => $main_sector_id],
    //                     ['account_type.for_backend' => false]
    //                 ],
    //             );


    //             if ($type == 'supervisor') {
    //                 $technicians_supervisors = $technicians_supervisors->andWhere(['account_type.name' => $type]);
    //             } else if ($type == 'technician') {
    //                 $technicians_supervisors = $technicians_supervisors->andWhere(['!=', 'account_type.name', 'supervisor']);
    //             }

    //             $technicians_supervisors = $technicians_supervisors->all();

    //             if ($datetime_input != null) {

    //                 $technicians_shifts = [];

    //                 $dateTime = new DateTime($datetime_input);
    //                 $timeOnly = $dateTime->format('H:i:s');
    //                 $dateOnly = $dateTime->format('Y-m-d');

    //                 if (!empty($division) && $division->has_shifts) {
    //                     $shift_id = Shift::find()
    //                         ->where(['<=', 'from_hour', $timeOnly])
    //                         ->andWhere(['>=', 'to_hour', $timeOnly])
    //                         ->one();

    //                     $technicians_shifts = TechnicianShift::find()->where(
    //                         [
    //                             'AND',
    //                             ['shift_id' => $shift_id->id],
    //                             ['date' => $dateOnly],
    //                             ['IN', 'technician_id', ArrayHelper::getColumn($technicians_supervisors, 'id')]
    //                         ]
    //                     )->all();
    //                 }

    //                 if (!empty($technicians_shifts)) {
    //                     $out = ArrayHelper::getColumn(
    //                         $technicians_shifts,
    //                         function ($model) {

    //                             $account_type_label = Technician::getTechnicianAccountTypeLabel($model->technician_id);
    //                             $name = @$model->technician->name;
    //                             $profession_name = @$model->technician->profession->name;

    //                             return ['id' => @$model->technician->id, 'name' =>  "{$account_type_label} | {$name} | {$profession_name}"];
    //                         },
    //                         false
    //                     );

    //                     if (!empty($order_id)) {
    //                         $assignees = RepairRequest::findOne($order_id)->assignees;

    //                         $selected = ArrayHelper::getColumn($assignees, 'id');

    //                         return ['output' => $out, 'selected' => $selected];
    //                     }

    //                     return ['output' => $out, 'selected' => ''];
    //                 }
    //             }

    //             if (!empty($technicians_supervisors)) {
    //                 $out = ArrayHelper::getColumn(
    //                     $technicians_supervisors,
    //                     function ($model) {

    //                         $account_type_label = Technician::getTechnicianAccountTypeLabel($model->id);
    //                         $name = @$model->name;
    //                         $profession_name = @$model->profession->name;

    //                         return ['id' => @$model->id, 'name' => "{$account_type_label} | {$name} | {$profession_name}"];
    //                     },
    //                     false
    //                 );

    //                 if (!empty($order_id)) {
    //                     $assignees = RepairRequest::findOne($order_id)->assignees;
    //                     $selected = ArrayHelper::getColumn($assignees, 'user_id');

    //                     return ['output' => $out, 'selected' => $selected];
    //                 }

    //                 return ['output' => $out, 'selected' => ''];
    //             }
    //         }
    //     }
    //     return ['output' => '', 'selected' => ''];
    // }

    public function actionSearchSupervisorTechnicians($type = null, $order_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {

            $location_code = $_POST['depdrop_all_params']['location-input-dropdown'];
            $datetime_input = $_POST['depdrop_all_params']['datetime_picker_input'];
            $category_id = $_POST['depdrop_all_params']['category_dropdown_id'];

            $technicians_supervisors = null;
            $union_technicians = null;
            $technicians_supervisors_assignee_count = null;

            $division = null;

            if (!empty($location_code)) { // division + main_sector + account_type ( super - tech )

                $location = Location::find()->where(['code' => $location_code])->one();
                $division = @$location->division;
                $main_sector_id = @$location->sector->main_sector_id;

                $technicians_supervisors = Technician::find()->joinWith('account')
                    ->innerJoin('account_type', 'account_type.id = account.type')
                    ->leftJoin('assignee a', 'a.user_id = technician.id AND a.status != ' . Assignee::STATUS_FREE)
                    ->select(['technician.id', 'technician.name', 'technician.profession_id', 'count(a.id) as number_of_repairs'])
                    ->filterWhere(
                        [
                            'AND',
                            ['technician.division_id' => @$division->id],
                            ['technician.status' => Technician::STATUS_ENABLED],
                            ['technician.main_sector_id' => $main_sector_id],
                            ['account_type.for_backend' => false]
                        ],
                    );

                $union_technicians = Technician::find()->joinWith('account')
                    ->innerJoin('account_type', 'account_type.id = account.type')
                    ->leftJoin('assignee a', 'a.user_id = technician.id AND a.status != ' . Assignee::STATUS_FREE)
                    ->select(['technician.id', 'technician.name', 'technician.profession_id', 'count(a.id) as number_of_repairs'])
                    ->filterWhere(
                        [
                            'AND',
                            ['technician.division_id' => @$division->id],
                            ['technician.status' => Technician::STATUS_ENABLED],
                            ['technician.main_sector_id' => $main_sector_id],
                            ['account_type.for_backend' => false]
                        ],
                    )->andWhere(['!=', 'account_type.name', 'supervisor']);

                if ($type == 'supervisor') {
                    $supervisors = clone $technicians_supervisors;
                    $supervisors = $supervisors->andWhere(['account_type.name' => $type])->groupBy(['technician.name', 'technician.id', 'technician.profession_id'])->asArray()->all();

                    if (!empty($supervisors)) {
                        if ($datetime_input != null && !empty($division) && $division->has_shifts) {
                            $technicians_shifts = [];

                            $dateTime = new DateTime($datetime_input);
                            $timeOnly = $dateTime->format('H:i:s');
                            $dateOnly = $dateTime->format('Y-m-d');

                            $shift_id = Shift::find()
                                ->where(['<=', 'from_hour', $timeOnly])
                                ->andWhere(['>=', 'to_hour', $timeOnly])
                                ->one();

                            if (!empty($shift_id)) {
                                $technicians_shifts = TechnicianShift::find()->where(
                                    [
                                        'AND',
                                        ['shift_id' => $shift_id->id],
                                        ['date' => $dateOnly],
                                        ['IN', 'technician_id', ArrayHelper::getColumn($supervisors, 'id')]
                                    ]
                                )->all();
                            }


                            if (empty($technicians_shifts)) {
                                return ['output' => '', 'selected' => ''];
                            } else {
                                $technician_ids = ArrayHelper::getColumn($technicians_shifts, 'technician_id');

                                // Initialize an empty result array
                                $resultArray = array();

                                // Loop through the first array
                                foreach ($supervisors as $item) {
                                    // Check if the 'id' in the first array exists in the second array
                                    if (in_array($item['id'], $technician_ids)) {
                                        // If it exists, add it to the result array
                                        $resultArray[] = $item;
                                    }
                                }

                                if (!empty($resultArray)) {
                                    $supervisors = $resultArray;
                                }
                            }
                        }

                        $out = ArrayHelper::getColumn(
                            $supervisors,
                            function ($model) {

                                $account_type_label = Technician::getTechnicianAccountTypeLabel($model["id"]);
                                $name = @$model["name"];
                                $profession_name = Profession::findOne($model['profession_id'])->name;
                                $assign_count = $model["number_of_repairs"];

                                return ['id' => @$model['id'], 'name' => "{$account_type_label} | {$name} ({$assign_count})"];
                            },
                            false
                        );

                        if (count($out) == 1) {
                            return ['output' => $out, 'selected' => $out];
                        }

                        return ['output' => $out, 'selected' => ''];
                    } else {
                        return ['output' => '', 'selected' => ''];
                    }
                } else if ($type == 'technician') {
                    $technicians_supervisors = $technicians_supervisors->andWhere(['!=', 'account_type.name', 'supervisor']);

                    if (!empty($category_id)) {

                        $profession_ids = ArrayHelper::getColumn(Category::findOne($category_id)->professions, 'id'); // test on window for now

                        // if (!empty($profession_ids)) {

                        $union_technicians = $union_technicians->andWhere([
                            'NOT IN',
                            'profession_id',
                            $profession_ids
                        ]); // alfred

                        $technicians_supervisors = $technicians_supervisors->andWhere([
                            'IN',
                            'profession_id',
                            $profession_ids
                        ]);
                        // }
                        //  else {
                        //     $technicians_supervisors = null;
                        //     $union_technicians = null;
                        // }
                    } else {
                        $union_technicians = null;
                    }

                    if ($datetime_input != null && !empty($division) && $division->has_shifts) {
                        $technicians_shifts = [];

                        $dateTime = new DateTime($datetime_input);
                        $timeOnly = $dateTime->format('H:i:s');
                        $dateOnly = $dateTime->format('Y-m-d');

                        $shift_id = Shift::find()
                            ->where(['<=', 'from_hour', $timeOnly])
                            ->andWhere(['>=', 'to_hour', $timeOnly])
                            ->one();

                        if (!empty($shift_id)) {
                            $technicians_shifts = TechnicianShift::find()->where(
                                [
                                    'AND',
                                    ['shift_id' => $shift_id->id],
                                    ['date' => $dateOnly],
                                    // ['IN', 'technician_id', ArrayHelper::getColumn($technicians_supervisors, 'id')]
                                ]
                            )->all();
                        }

                        if (empty($technicians_shifts)) {
                            return ['output' => '', 'selected' => ''];
                        }



                        $technician_ids = ArrayHelper::getColumn($technicians_shifts, 'technician_id');


                        if (!empty($technician_ids)) {
                            $technicians_supervisors = $technicians_supervisors->andWhere([
                                "IN",
                                Technician::tableName() . '.id',
                                $technician_ids
                            ]);

                            if (!empty($union_technicians)) {
                                $union_technicians = $union_technicians->andWhere([
                                    "IN",
                                    Technician::tableName() . '.id',
                                    $technician_ids
                                ]);
                            }
                        }
                    }

                    if (!empty($technicians_supervisors))
                        $technicians_supervisors = $technicians_supervisors->groupBy(['technician.name', 'technician.id', 'technician.profession_id'])->asArray()->all();

                    if (!empty($union_technicians))
                        $union_technicians = $union_technicians->groupBy(['technician.name', 'technician.id', 'technician.profession_id'])->asArray()->all();

                    if (!empty($technicians_supervisors) || !empty($union_technicians)) {

                        if (!empty($technicians_supervisors)) {

                            // $technicians_supervisors_ids = ArrayHelper::getColumn($technicians_supervisors, 'id');
                            // $subQ1 = Assignee::find()->select(['user_id', 'max(updated_at) as updated_at'])->where(['IN', 'user_id', $technicians_supervisors_ids])->groupBy('user_id');
                            // $subQ12 = Assignee::find()->alias('sub2')
                            //     ->innerJoin(['sub1' => $subQ1], ['sub1.user_id' => new Expression('sub2.user_id'), 'sub1.updated_at' => new Expression('sub2.updated_at')])
                            //     ->createCommand()
                            //     ->rawSql;

                            // print_r($subQ12);
                            // exit();

                            $out = ArrayHelper::getColumn(
                                $technicians_supervisors,
                                function ($model) {
                                    $account_type_label = Technician::getTechnicianAccountTypeLabel($model["id"]);
                                    $name = @$model["name"];
                                    $profession_name = Profession::findOne($model['profession_id'])->name;
                                    $assign_count = $model["number_of_repairs"];


                                    return ['id' => @$model["id"], 'name' => "<span style='color:black;'> {$account_type_label} | {$name} | {$profession_name} ($assign_count)  </span>"];
                                },
                                false
                            );
                        }


                        if (!empty($union_technicians)) {
                            $out = ArrayHelper::merge(
                                $out,
                                ArrayHelper::getColumn(
                                    $union_technicians,
                                    function ($model) {
                                        $account_type_label = Technician::getTechnicianAccountTypeLabel($model["id"]);
                                        $name = @$model["name"];
                                        $profession_name = Profession::findOne($model['profession_id'])->name;
                                        $assign_count = $model["number_of_repairs"];


                                        return ['id' => @$model["id"], 'name' => "<span style='color:red;'> {$account_type_label} | {$name} | {$profession_name} ($assign_count)  </span>"];
                                    },
                                    false
                                )
                            );
                        }

                        if (!empty($order_id)) {
                            $assignees = RepairRequest::findOne($order_id)->assignees;

                            $selected = ArrayHelper::getColumn($assignees, 'user_id');

                            return ['output' => $out, 'selected' => $selected];
                        }

                        return ['output' => $out, 'selected' => ''];
                    }
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionGetSectorData($sector_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $sector = Sector::findOne($sector_id);
        if ($sector) {
            return ['country_id' => $sector->country_id, 'state_id' => $sector->state_id, 'city_id' => $sector->city_id];
        } else {
            return ['country_id' => null, 'state_id' => null, 'city_id' => null];
        }
    }
    public function actionGetDataBySector()
    {

        if ($this->request->post()) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $sector = Sector::findOne($this->request->post()['sector_id']);

            return [
                'country' => @$sector->country->name,
                'state' => @$sector->state->name,
                'city' => @$sector->city->name,
            ];
        }
    }
    public function actionSaveHiddenAttributes()
    {
        $fadedColumns = Yii::$app->request->post('faded_columns');
        $user_id = Yii::$app->user->id;
        $page_id = Yii::$app->request->post('controller_id');
        $user_grid = UserGrid::find()->where(['user_id' => $user_id])->andWhere(['page_id' => $page_id])->one();

        $existingValue = $user_grid ? $user_grid->value : '';
        $existingColumns = $existingValue ? explode(',', $existingValue) : [];
        $newColumns = array_unique(array_merge($existingColumns, explode(',', $fadedColumns)));
        $updatedValue = implode(',', $newColumns);

        if (empty($user_grid)) {
            $model = new UserGrid();
            $model->user_id = $user_id;
            $model->page_id = $page_id;
            $model->value = $updatedValue;
        } else {
            $user_grid->value = $updatedValue;
            $model = $user_grid;
        }
        if (!empty($updatedValue)) {
            if ($model->save()) {
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    public function actionSaveShownAttributes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $hiddenAttributes = Yii::$app->request->post('hidden-attributes', []);

            $page_id = Yii::$app->request->post('controller_id', '');
            $user_grid = UserGrid::find()->where(['user_id' => Yii::$app->user->id])->andWhere(['page_id' => $page_id])->one();

            if ($user_grid) {
                $valuesArray = explode(',', $user_grid->value);
                $valuesArray = array_diff($valuesArray, $hiddenAttributes);
                $user_grid->value = implode(',', $valuesArray);
                $user_grid->save();
                Yii::$app->session->set('hiddenAttributes', $hiddenAttributes);
                Yii::$app->getSession()->addFlash("success", "The selected columns have been shown");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionGetCategories()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_POST['depdrop_all_params'])) {

            $sector_id = $_POST['depdrop_all_params']['sector_dropdown'];

            if (!empty($sector_id)) {
                $categories = Category::find()->all();

                $out = ArrayHelper::getColumn(
                    $categories,
                    function ($model) {
                        return ['id' => @$model->id, 'name' => $model->name];
                    },
                    false
                );

                return ['output' => $out, 'selected' => ''];
            }

            return ['output' => '', 'selected' => ''];
        }
    }

    public function actionGetAdminNotifications($count)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];

        $notifications = AdminNotifications::find()
            // ->where(['technician_id' => $userId])
            ->where([
                'AND',
                // ['technician_id' => $userId],
                ['seen' => false],
            ])
            ->limit(20)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $unseen = AdminNotifications::find()
            ->where([
                'AND',
                // ['technician_id' => $userId],
                ['seen' => false],
            ])
            ->count();

        if ($count < $unseen) {
            $out['notify'] = true;
        }

        $rend = $this->renderPartial('@common/components/notification/widgets/views/admin-notifications-panel', [
            'notifications' => $notifications,
            'unseen' => $unseen,
        ]);

        $out['response'] = $rend;

        $out['unseen'] = $unseen;

        return $out;
    }

    public function actionMarkAllAsRead()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];

        AdminNotifications::updateAll(['seen' => true], ['seen' => false]);

        $rend = $this->renderPartial('@common/components/notification/widgets/views/admin-notifications-panel', [
            'notifications' => [],
            'unseen' => 0,
        ]);

        $out['response'] = $rend;

        $out['notify'] = true;

        return $out;
    }
}
