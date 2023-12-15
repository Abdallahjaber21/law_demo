<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin\controllers;

use common\config\includes\P;
use common\models\Account;
use common\models\CoordinatesIssue;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Log;
use common\models\RepairRequest;
use common\models\search\CoordinatesIssueSearch;
use common\models\search\EquipmentSearch;
use common\models\search\LocationEquipmentsSearch;
use common\models\search\LocationSearch;
use common\models\search\LogSearch;
use common\models\search\RepairRequestSearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\web\Controller;

/**
 * Description of ExportController
 *
 * @author Tarek K. Ajaj
 */
class ExportController extends Controller
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
                        'allow' => true,
                        //'actions' => ['users'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }



    public function prepareData($models, $capitalizeheader = true)
    {

        $data = [];
        if (!empty($models)) {
            $keys = array_keys($models[0]->toArray());
            $firstRow = [];
            foreach ($keys as $key) {
                $firstRow[] = $capitalizeheader ? Inflector::camel2words($key, true) : $key;
            }
            $data[] = $firstRow;
            foreach ($models as $key => $model) {
                $data[] = array_values($model->toArray());
            }

            return $this->export($data);
        }
        Yii::$app->getSession()->addFlash("warning", Yii::t("app", "There are no data to export"));
        return $this->goBack();
    }

    private function export($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($data, null, 'A1');
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->setOffice2003Compatibility(true);
        $folderPath = Yii::getAlias("@runtime") . DIRECTORY_SEPARATOR . "tmp";
        if (!file_exists($folderPath)) {
            mkdir($folderPath);
        }

        $path = $folderPath . DIRECTORY_SEPARATOR .  "report_export_" . time() . ".xlsx";
        $writer->save($path);

        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
    }

    public function actionEquipments()
    {
        if (Yii::$app->getRequest()->isPost) {
            Yii::$app->user->returnUrl = ['equipment/index'];

            $searchModel = new EquipmentSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            $dataProvider->query->with(['location', 'location.sector']);
            $models = $dataProvider->getModels();

            Equipment::$return_fields = Equipment::FIELDS_EXPORT;
            return $this->prepareData($models, false);
        }
        return $this->redirect(['equipment/index']);
    }
    public function actionRepairRequests()
    {
        if (Yii::$app->getRequest()->isPost) {
            Yii::$app->user->returnUrl = array_merge(['site/labor-charge'], Yii::$app->request->get());



            $searchModel = new RepairRequestSearch();
            $params = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);
            $dataProvider->pagination = false;
            $dataProvider->query
                ->andWhere(['>', 'labor_charge', 0])
                ->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
            $models = $dataProvider->getModels();
            RepairRequest::$return_fields = RepairRequest::FIELDS_EXPORT;
            return $this->prepareData($models, false);
        }
        return $this->redirect(array_merge(['site/labor-charge'], Yii::$app->request->get()));
    }
    public function actionLocations()
    {
        if (Yii::$app->getRequest()->isPost) {
            Yii::$app->user->returnUrl = ['location/index'];

            $searchModel = new LocationSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            $dataProvider->query->with(['sector']);
            $models = $dataProvider->getModels();
            Location::$return_fields = Location::FIELDS_EXPORT;
            return $this->prepareData($models, false);
        }
        return $this->redirect(['location/index']);
    }

    public function actionLocationEquipments()
    {
        if (Yii::$app->request->get()) {
            Yii::$app->user->returnUrl = ['import/location-equipments'];

            $division = Division::findOne(Yii::$app->request->get('division'));

            $searchModel = new LocationEquipmentsSearch();
            $searchModel->division_id = $division->id;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
            $dataProvider->pagination = false;
            // $dataProvider->query->andWhere(['division_id' => Yii::$app->request->get('division')]);
            // $dataProvider->query->with(['location'])->where(['location.divisi']);
            $models = $dataProvider->getModels();

            if ($division->id == Division::DIVISION_MALL) {
                LocationEquipments::$return_fields = LocationEquipments::FIELDS_EXPORT_MALL;
            } else if ($division->id == Division::DIVISION_PLANT) {
                LocationEquipments::$return_fields = LocationEquipments::FIELDS_EXPORT_PLANT;
            } else if ($division->id == Division::DIVISION_VILLA) {
                LocationEquipments::$return_fields = LocationEquipments::FIELDS_EXPORT_VILLA;
            }
            return $this->prepareData($models, false);
        }
        return $this->redirect(['import/location-equipments']);
    }

    public function actionCoordinateIssues()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->user->returnUrl = array_merge(['coordinates-issue/index'], Yii::$app->request->get());

            $searchModel = new CoordinatesIssueSearch();
            $params = Yii::$app->request->queryParams;
            $dataProvider = $searchModel->search($params);
            $dataProvider->pagination = false;
            $models = $dataProvider->getModels();

            CoordinatesIssue::$return_fields = CoordinatesIssue::FIELDS_EXPORT;
            return $this->prepareData($models);
        }
        return $this->redirect(array_merge(['coordinates-issue/index'], Yii::$app->request->get()));
    }

    public function actionTechnicianLogs($id)
    {
        if (Yii::$app->request->get()) {
            Yii::$app->user->returnUrl = ['repair-request/view', 'id' => $id];

            $logs = Log::find()->where(['repair_request_id' => $id, 'type' => Log::TYPE_TECHNICIAN])->all();

            return $this->prepareData($logs, false);
        }
        return $this->redirect(['repair-request/view', 'id' => $id]);
    }
}
