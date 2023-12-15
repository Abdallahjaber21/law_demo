<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\EquipmentCaValue;
use common\models\Location;
use Yii;
use common\models\LocationEquipments;
use common\models\search\LocationEquipmentsSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;
use yii\filters\AccessControl;


/**
 * LocationEquipmentsController implements the CRUD actions for LocationEquipments model.
 */
class LocationEquipmentsController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_VIEW),
                        'actions' => ['index', 'get-layer-values', 'get-ca-values', 'view', 'location-equipments'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_NEW),
                        'actions' => ['create', 'create-equipment'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LocationEquipments models.
     * @return mixed
     */
    public function actionIndex($location_id = null)
    {
        $searchModel = new LocationEquipmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $equipment_custom_attributes = EquipmentCa::find()->where(['division_id' => Location::findOne($location_id)->division_id])->asArray()->all();
        if (!empty($equipment_custom_attributes)) {
            foreach ($equipment_custom_attributes as &$ssss) {
                $ssss['equipment_id'] = 0;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'location_id' => $location_id,
            'equipment_custom_attributes' => $equipment_custom_attributes,
            'location_equipments' => LocationEquipments::find()->where(['location_id' => $location_id])->all()
        ]);
    }
    // public function actionLocationEquipments()
    // {
    //     $searchModel = new LocationEquipmentsSearch();
    //     $params = Yii::$app->request->queryParams;
    //     $dataProvider = $searchModel->search($params);
    //     $dataProvider = new ActiveDataProvider([
    //         // 'query' => LocationEquipments::find()->joinWith('equipmentCaValues'),
    //         'query' => LocationEquipments::find()->leftJoin("equipment_ca_value", "location_equipments.id = equipment_ca_value.location_equipment_id"),

    //     ]);



    //     $query = Yii::$app->request->get('query');

    //     if ((isset($_POST['query']) && !empty($_POST['query'])) || (isset($query) && !empty($query))) {
    //         if ((isset($_POST['query']) && !empty($_POST['query']))) {
    //             $searchTerm = $_POST['query'];
    //         } else {
    //             $searchTerm = $query;
    //         }
    //         $dataProvider->query->andWhere([
    //             'OR',
    //             ['=', LocationEquipments::tableName() . '.id', $searchTerm],
    //             ['=', LocationEquipments::tableName() . '.code', $searchTerm],
    //             ['LIKE', LocationEquipments::tableName() . '.value', $searchTerm],
    //             ['=', LocationEquipments::tableName() . '.meter_value', $searchTerm],
    //             ['=', LocationEquipments::tableName() . '.chassie_number', $searchTerm],
    //             ['LIKE', EquipmentCaValue::tableName() . '.value', $searchTerm],
    //         ]);
    //     }
    //     $dataProvider->query->groupBy('location_equipments.id');
    //     // print_r($dataProvider->query->createCommand()->rawSql);
    //     // exit;
    //     return $this->render('location-equipments', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //     ]);
    // }
    public function actionLocationEquipments()
    {
        $searchModel = new LocationEquipmentsSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        // Add your custom join condition
        $dataProvider->query->leftJoin("equipment_ca_value", "location_equipments.id = equipment_ca_value.location_equipment_id");

        $query = Yii::$app->request->get('query');

        if ((isset($_POST['query']) && !empty($_POST['query'])) || (isset($query) && !empty($query))) {
            if ((isset($_POST['query']) && !empty($_POST['query']))) {
                $searchTerm = $_POST['query'];
            } else {
                $searchTerm = $query;
            }
            $dataProvider->query->andWhere([
                'OR',
                ['=', LocationEquipments::tableName() . '.id', $searchTerm],
                ['=', LocationEquipments::tableName() . '.code', $searchTerm],
                ['LIKE', LocationEquipments::tableName() . '.value', $searchTerm],
                ['=', LocationEquipments::tableName() . '.meter_value', $searchTerm],
                ['=', LocationEquipments::tableName() . '.chassie_number', $searchTerm],
                ['LIKE', EquipmentCaValue::tableName() . '.value', $searchTerm],
            ]);
        }

        $dataProvider->query->groupBy('location_equipments.id');

        return $this->render('location-equipments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetLayerValues()
    {
        $location_id = Yii::$app->request->get('location_id');
        $layerName = Yii::$app->request->get('layer');

        // $query = LocationEquipments::find()
        //     ->select(['value'])
        //     ->joinWith('location')
        //     ->where(['like', 'value', '%"layer":"' . $layerName . '"%', false]);

        $divisionId = Location::findOne($location_id)->division_id; //20
        $locationIds = Location::find()
            ->select(['id'])
            ->where(['division_id' => $divisionId])
            ->column();

        $query = LocationEquipments::find()
            ->select(['value'])
            ->joinWith('location')
            ->where(['like', 'value', '%"layer":"' . $layerName . '"%', false])
            ->andWhere(['IN', 'location_equipments.location_id', $locationIds]);

        $results = $query->all();
        $layerValues = [];
        $uniqueValues = [];
        foreach ($results as $result) {
            $data = Json::decode($result->value);
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['layer']) && isset($item['value']) && $item['layer'] == $layerName) {
                        $value = $item['value'];
                        if (!empty($value) && !in_array(strtolower($value), array_map('strtolower', $uniqueValues))) {
                            $uniqueValues[] = $value;
                            $layerValues[] = $value;
                        }
                    }
                }
            }
        }
        $layerValues = array_map('strtolower', $layerValues);
        sort($layerValues);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $layerValues;
    }

    public function actionGetCaValues()
    {
        $location_id = Yii::$app->request->get('location_id');
        $id = Yii::$app->request->get('id');
        $query = EquipmentCaValue::find()
            ->joinWith('equipmentCa')
            ->where(['equipment_ca_id' => $id]);

        $results = $query->all();

        $caValues = [];
        $uniqueValues = [];
        foreach ($results as $result) {
            $value = $result->value;
            if (!empty($value) && !in_array(strtolower($value), array_map('strtolower', $uniqueValues))) {
                $uniqueValues[] = $value;
                $caValues[] = $value;
            }
        }

        $caValues = array_map('strtolower', $caValues);
        sort($caValues);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $caValues;
    }


    /**
     * Displays a single LocationEquipments model.
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
     * Creates a new LocationEquipments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LocationEquipments();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LocationEquipments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $LocationEquipment = Yii::$app->request->post()['LocationEquipments'];
            $value = Equipment::GetJsonSegmentValue($LocationEquipment['value']);
            $model->value = $value;

            $custom_attributes_models = @$LocationEquipment['custom_attributes'];

            if (!empty($custom_attributes_models)) {
                foreach ($custom_attributes_models as $index => $customs) {
                    $Cmodel = EquipmentCaValue::findOne($index);
                    $Cmodel->value = $customs;
                    $Cmodel->save();
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('warning', $model->id . ' Updated !!');
            }
            // return $this->redirect(['index', 'location_id' => $model->location_id]);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateEquipment()
    {
        if ((Yii::$app->request->post())) {

            $selected_equipments_codes_values = Yii::$app->request->post()['LocationEquipments'];

            $location_id = Yii::$app->request->post('location_id');

            LocationEquipments::SaveEquipment($location_id, $selected_equipments_codes_values);

            return $this->redirect(['index', 'location_id' => $location_id]);
        }
    }

    /**
     * Deletes an existing LocationEquipments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $parentmodel = Location::find()->where(['id' => $model->location_id])->one();
        $parentmodel = $parentmodel->status;
        if ($parentmodel != Location::STATUS_DELETED) {
            if ($model->status == LocationEquipments::STATUS_ENABLED) {
                $model->status = LocationEquipments::STATUS_DELETED;
            } else {

                $model->status = LocationEquipments::STATUS_ENABLED;
            }
            $model->save();
            if ($model->status == LocationEquipments::STATUS_ENABLED) {

                Yii::$app->session->addFlash("success", "Location Equipment has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Location Equipment has been deleted");
            }
            return $this->redirect(['index', 'location_id' => $model->location_id]);
        } else {
            Yii::$app->session->addFlash("error", "The location of this location equipment has status deleted, and this location equipment cannot be undeleted.");
            return $this->redirect(['index', 'location_id' => $model->location_id]);
        }
    }

    /**
     * Finds the LocationEquipments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationEquipments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = LocationEquipments::findOne($id)) !== null) {
            //        if (($model = LocationEquipments::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
