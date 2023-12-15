<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Equipment;
use common\models\EquipmentMaintenanceBarcode;
use common\models\EquipmentPath;
use common\models\Location;
use common\models\Account;
use common\models\MaintenanceVisit;
use common\models\RepairRequest;
use common\models\search\EquipmentSearch;
use common\models\users\Admin;
use common\models\EquipmentCa;
use common\models\EquipmentCaValue;
use common\models\EquipmentType;
use yii\helpers\Html;
use kartik\mpdf\Pdf;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;;


/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class EquipmentController extends Controller
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
                        'allow'   => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_VIEW),
                        'actions' => ['index', 'view', 'ajax-segment', 'get-equipmentca'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_EXPORT),
                        'actions' => ['export'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MANAGEMENT_EQUIPMENT_PAGE_NEW),
                        'actions' => ['create', 'import-barcodes', 'delete-barcode', 'delete-barcodes', 'delete-multiple'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MANAGEMENT_LOCATION_PAGE_VIEW),
                        'actions' => ['map'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Equipment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentSearch();
        $searchParams = Yii::$app->request->queryParams;
        $searchParams['EquipmentSearch']['sector_id'] = Admin::activeSectorsIds();
        $dataProvider = $searchModel->search($searchParams);
        if (Yii::$app->request->get('export') === 'pdf') {

            if ($dataProvider instanceof yii\data\ActiveDataProvider) {
                $dataProvider->pagination = false;
            }
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A3,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_DOWNLOAD,
                'options' => [
                    'title' => 'Locations',
                    'margin' => [
                        'top' => 20,
                        'right' => 15,
                        'bottom' => 20,
                        'left' => 15,
                    ],
                ],
                'methods' => [
                    'SetFooter' => ['|Page {PAGENO}|'],
                ],
            ]);
            $pdf->filename = 'equipment-report.pdf';
            $allContent = $this->renderPartial('export', compact('searchModel', 'dataProvider'));
            $pdf->content = $allContent;
            return $pdf->render();
        }
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Equipment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($equipment_type_id = null)
    {
        $model = new Equipment();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $model->name .  ' Has been created!');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'equipment_type_id' => $equipment_type_id
            ]);
        }
    }

    /**
     * Updates an existing Equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', $model->name .  ' Has been updated!');

                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionGetEquipmentca($divisionId, $equipmentId)
    {
        $equipmentCaList = EquipmentCa::find()
            ->where(['division_id' => $divisionId])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $equipmentCaData = [];

        foreach ($equipmentCaList as $equipmentCa) {
            $equipmentCaValueModel = EquipmentCaValue::findOne([
                'equipment_id' => $equipmentId,
                'equipment_ca_id' => $equipmentCa->id
            ]);
            $equipmentCaData[] = [
                'id' => $equipmentCa->id,
                'name' => $equipmentCa->name,
                'value' => isset($equipmentCaValueModel->value) ? $equipmentCaValueModel->value : '',
            ];
        }

        return \yii\helpers\Json::encode(['success' => true, 'equipmentCaList' => $equipmentCaData]);
    }



    public function actionAjaxSegment()
    {
        if (Yii::$app->request->post()) {
            $loc_id = Yii::$app->request->post('location_id');
            $response = [];
            $segment_id = @Location::findOne($loc_id)->segmentPath->id;

            if (!empty($segment_id)) {
                $response = ['response' => $segment_id];
            } else {
                $response = ['response' => null];
            }

            return \yii\helpers\Json::encode($response);
        }

        throw new \yii\web\BadRequestHttpException("Error");
    }

    public function actionImportBarcodes($id)
    {
        $model = $this->findModel($id);
        $uploadedFile = UploadedFile::getInstanceByName("importbarcodes");
        if (!empty($uploadedFile) && $uploadedFile instanceof UploadedFile) {
            $path = Yii::getAlias("@static/upload/importbarcodes.csv");
            if ($uploadedFile->saveAs($path)) {
                if (($handle = fopen($path, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
                        $embarcode = EquipmentMaintenanceBarcode::find()
                            ->where([
                                'AND',
                                ['equipment_id' => $id],
                                ['barcode' => $data[1]]
                            ])
                            ->one();
                        if (empty($embarcode)) {
                            $embarcode = new EquipmentMaintenanceBarcode();
                            $embarcode->equipment_id = $id;
                        }
                        $embarcode->status = EquipmentMaintenanceBarcode::STATUS_ENABLED;
                        $embarcode->location = $data[0];
                        $embarcode->barcode = $data[1];
                        $embarcode->save();
                    }
                    fclose($handle);
                }
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionDeleteBarcode($id, $bid)
    {

        EquipmentMaintenanceBarcode::deleteAll(['id' => $bid]);

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteBarcodes($id)
    {
        $barcodes = Yii::$app->request->post("barcode");
        foreach ($barcodes as $barcodeId => $checked) {
            if ($checked) {
                EquipmentMaintenanceBarcode::deleteAll(['id' => $barcodeId]);
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Deletes an existing Equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $parentmodel = EquipmentType::find()->where(['id' => $model->equipment_type_id])->one();
        $parentmodel = $parentmodel->status;
        if ($parentmodel != EquipmentType::STATUS_DELETED) {
            if ($model->status == Equipment::STATUS_ENABLED) {
                $model->status = Equipment::STATUS_DELETED;
            } else {
                $model->status = Equipment::STATUS_ENABLED;
            }
            $model->save();
            if ($model->status == Equipment::STATUS_ENABLED) {

                Yii::$app->session->addFlash("success", "Equipment has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Equipment has been deleted");
            }
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->addFlash("error", "The equipment type of this equipment is deleted, and this equipemnt cannot be undeleted.");
            return $this->redirect(['index']);
        }
    }


    /**
     * Finds the Equipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Equipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $equipment = Equipment::findOne($id);
        $query = Equipment::find()->where(['id' => $id]);
        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([Equipment::tableName() . '.division_id' => $equipment->division_id]);
        } else {
            $query->andFilterWhere([Equipment::tableName() . '.division_id' => Yii::$app->user->identity->division_id]);
        }
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDeleteMultiple()
    {
        $idsPost = Yii::$app->request->post("selected_ids");
        $ids = explode(",", $idsPost);
        $errors = 0;
        $successes = 0;
        if (!empty($ids)) {
            $models = Equipment::find()->filterWhere([
                'id' => $ids
            ])->all();
            foreach ($models as $index => $model) {
                if (!empty($model)) {
                    if (!$model->delete()) {
                        $errors++;
                        Yii::error($model->errors);
                    } else {
                        $successes++;
                    }
                }
            }
        }
        if ($errors > 0) {
            Yii::$app->session->addFlash("danger", "{$errors} equipments were not deleted");
        }
        if ($successes > 0) {
            Yii::$app->session->addFlash("info", "{$successes} equipments were deleted successfully");
        }
        return $this->redirect(['index']);
    }

    public function actionMap($filter = null, $location_id = null)
    {
        if (!empty($location_id)) {
            $locations = Location::find()->where(['id' => $location_id])
                ->andFilterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->asArray()->all();
            $countUnits = Equipment::find()
                ->joinWith(['location'], false)
                ->select(['location_id', 'count(*) as c'])
                ->where(['location_id' => ArrayHelper::getColumn($locations, 'id', false)])
                ->andFilterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->groupBy(['location_id'])
                ->indexBy('location_id')
                ->asArray()
                ->all();
            $activeLocations = RepairRequest::find()
                ->leftJoin('equipment', ['repair_request.equipment_id' => new Expression('equipment.id')])
                ->select(['equipment.location_id', 'repair_request.status', 'repair_request.type', 'repair_request.id'])
                ->where([
                    'repair_request.status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,


                        RepairRequest::STATUS_CHECKED_IN,
                    ]
                ])
                ->andWhere(['equipment.location_id' => ArrayHelper::getColumn($locations, 'id', false)])
                //            ->createCommand()->rawSql;
                ->asArray()
                ->indexBy('location_id')
                ->all();
            $activeMaintenances = MaintenanceVisit::find()
                ->select(['location_id'])
                ->where([
                    'status' => MaintenanceVisit::STATUS_ENABLED
                ])
                ->andWhere(['location_id' => ArrayHelper::getColumn($locations, 'id', false)])
                ->asArray()
                ->indexBy('location_id')
                ->all();
            return $this->render('map', [
                "locations"          => $locations,
                "activeLocations"    => $activeLocations,
                "activeMaintenances" => $activeMaintenances,
                "countUnits"         => $countUnits,
            ]);
        }
        if ($filter == null || $filter == "all") {
            $locations = Location::find()
                ->filterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->asArray()->all();
            $countUnits = Equipment::find()
                ->joinWith(['location'], false)
                ->select(['location_id', 'count(*) as c'])
                ->filterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->groupBy(['location_id'])
                ->indexBy('location_id')
                ->asArray()
                ->all();
            $activeLocations = RepairRequest::find()
                ->leftJoin('equipment', ['repair_request.equipment_id' => new Expression('equipment.id')])
                ->select(['equipment.location_id', 'repair_request.status', 'repair_request.type', 'repair_request.id'])
                ->where([
                    'repair_request.status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,


                        RepairRequest::STATUS_CHECKED_IN,
                    ]
                ])
                //            ->createCommand()->rawSql;
                ->asArray()
                ->indexBy('location_id')
                ->all();
            $activeMaintenances = MaintenanceVisit::find()
                ->select(['location_id'])
                ->where([
                    'status' => MaintenanceVisit::STATUS_ENABLED
                ])
                ->asArray()
                ->indexBy('location_id')
                ->all();
            return $this->render('map', [
                "locations"          => $locations,
                "activeLocations"    => $activeLocations,
                "activeMaintenances" => $activeMaintenances,
                "countUnits"         => $countUnits,
            ]);
        }

        if ($filter == "repair") {
            $activeLocations = RepairRequest::find()
                ->leftJoin('equipment', ['repair_request.equipment_id' => new Expression('equipment.id')])
                ->select(['equipment.location_id', 'repair_request.status', 'repair_request.type', 'repair_request.id'])
                ->where([
                    'repair_request.status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,


                        RepairRequest::STATUS_CHECKED_IN,
                    ]
                ])
                //            ->createCommand()->rawSql;
                ->asArray()
                ->indexBy('location_id')
                ->all();

            $locations = Location::find()
                ->where(['id' => ArrayHelper::getColumn($activeLocations, 'location_id', false)])
                ->andFilterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->asArray()
                ->all();
            $countUnits = Equipment::find()
                ->select(['location_id', 'count(*) as c'])
                ->where(['location_id' => ArrayHelper::getColumn($activeLocations, 'location_id', false)])
                ->groupBy(['location_id'])
                ->indexBy('location_id')
                ->asArray()
                ->all();

            $activeMaintenances = [];
            return $this->render('map', [
                "locations"          => $locations,
                "activeLocations"    => $activeLocations,
                "activeMaintenances" => $activeMaintenances,
                "countUnits"         => $countUnits,
            ]);
        }

        if ($filter == "maintenance") {
            $activeLocations = [];

            $activeMaintenances = MaintenanceVisit::find()
                ->select(['location_id'])
                ->where([
                    'status' => MaintenanceVisit::STATUS_ENABLED
                ])
                ->asArray()
                ->indexBy('location_id')
                ->all();

            $locations = Location::find()
                ->where(['id' => ArrayHelper::getColumn($activeMaintenances, 'location_id', false)])
                ->andFilterWhere([Location::tableName() . '.sector_id' => Admin::activeSectorsIds()])
                ->asArray()
                ->all();
            $countUnits = Equipment::find()
                ->select(['location_id', 'count(*) as c'])
                ->where(['location_id' => ArrayHelper::getColumn($activeMaintenances, 'location_id', false)])
                ->groupBy(['location_id'])
                ->indexBy('location_id')
                ->asArray()
                ->all();

            return $this->render('map', [
                "locations"          => $locations,
                "activeLocations"    => $activeLocations,
                "activeMaintenances" => $activeMaintenances,
                "countUnits"         => $countUnits,
            ]);
        }
    }
}
