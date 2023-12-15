<?php

namespace admin\controllers;

use common\config\includes\P;
use common\data\Countries;
use common\models\Account;
use common\models\Division;
use common\models\MainSector;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\EquipmentCaValue;
use Yii;
use kartik\mpdf\Pdf;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\search\LocationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Sector;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_LOCATION_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    // [
                    //     'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin"),
                    //     'actions' => ['delete'],
                    //     'roles' => ['@'],
                    // ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_PAGE_NEW),
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_LOCATION_PAGE_NEW),
                        'actions' => ['clone'],
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
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->get('export') === 'pdf') {
            $searchModel = new LocationSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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



            $pdf->filename = 'location-report.pdf';

            // Render the content for the entire dataset
            $allContent = $this->renderPartial('export', compact('searchModel', 'dataProvider'));

            $pdf->content = $allContent;
            return $pdf->render();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Location model.
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
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($model->owner_phone)) {
                if (Account::ValidateNumber($model->owner_phone)) {
                    if ($model->save()) {
                        Yii::$app->getSession()->addFlash("warning", "Location: " . $model->name . " has been created");
                        return $this->redirect(['index']);
                    } else {
                        print_r($model->errors);
                        exit;
                    }
                    return $this->redirect(['create']);
                } else {
                    Yii::$app->getSession()->addFlash("error", $model->owner_phone . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->owner_phone)));
                }
            } else {
                if ($model->save()) {
                    Yii::$app->getSession()->addFlash("warning", "Location: " . $model->name . " has been created");
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->owner_phone)) {
                if (Account::ValidateNumber($model->owner_phone)) {
                    if ($model->save()) {
                        Yii::$app->getSession()->addFlash("warning", "Location: " . $model->name . " is updated successfully");
                        return $this->redirect(['index']);
                    }
                    return $this->redirect(['update']);
                } else {
                    Yii::$app->getSession()->addFlash("error", $model->owner_phone . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->owner_phone)));
                }
            } else {
                if ($model->save()) {
                    Yii::$app->getSession()->addFlash("warning", "Location: " . $model->name . " is updated successfully");
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionClone()
    {
        if ($this->request->post()) {

            $apply_for_all = $this->request->post('LocationEquipments')['apply_all'];
            $post = $this->request->post('Location');
            $current_location = Location::findOne($post['id']);
            $models_to_be_cloned = null;

            if (!empty($apply_for_all) && $apply_for_all) { // apply for all
                $models_to_be_cloned = Location::find()->where(['division_id' => $current_location->division->id])->andWhere(['<>', 'id', $current_location->id])->all();

                foreach ($models_to_be_cloned as $location) {
                    // Clone For The New Location
                    $this->CloneLocation($current_location, $location);
                }
            } else {
                $code = $this->request->post('LocationEquipments')['code'];

                $models_to_be_cloned = Location::find()->where(['code' => $code])->one();

                if (@$models_to_be_cloned->division->id != $current_location->division->id) {
                    Yii::$app->session->setFlash('error', "The Code Provided Doesn't Seem Related To The Same Division!!");
                    return $this->redirect('index');
                }

                if (@$models_to_be_cloned->id == $current_location->id) {
                    Yii::$app->session->setFlash('error', "The Selected Location Is The Same As The One Wanted To Be Cloned!!");
                    return $this->redirect('index');
                }

                // Clone For The New Location
                $this->CloneLocation($current_location, $models_to_be_cloned);
            }
        }

        return $this->redirect('index');
    }

    public function CloneLocation($current_location, $new_location)
    {
        $current_location_equipments = $current_location->locationEquipments;

        // Delete All Old Location Equipments If Exists
        $new_location_equipments = $new_location->locationEquipments;

        if (!empty($new_location_equipments)) {
            foreach ($new_location_equipments as $locc) {
                $locc->delete();
            }
        }

        foreach ($current_location_equipments as $curr) {

            $model = new LocationEquipments();
            $model->division_id  = $current_location->division_id;
            $model->location_id  = $new_location->id;
            $model->equipment_id = $curr->equipment_id;
            $model->driver_id = $curr->driver_id;
            $model->code = $curr->code;
            $model->value = $curr->value;
            $model->remarks = $curr->remarks;
            $model->status = $curr->status;

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', "Attributes Cloned From: " . $current_location->name . " To: " .  $new_location->name . " Successfully!!");
                $current_custom_attributes = $curr->equipmentCaValues;

                if (!empty($current_custom_attributes)) {
                    foreach ($current_custom_attributes as $attr) {
                        $custom_model = new EquipmentCaValue();
                        $custom_model->location_equipment_id = $model->id;
                        $custom_model->equipment_ca_id = $attr->equipment_ca_id;
                        $custom_model->equipment_id = $attr->equipment_id;
                        $custom_model->value = $attr->value;
                        $custom_model->status = $attr->status;
                        $custom_model->save(false);
                    }
                }
            }
        }

        return true;
    }


    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $name = $this->findModel($id)->name;
    //     $this->findModel($id)->delete();
    //     Yii::$app->session->addFlash("danger", "Location " . $name . " is deleted");

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    // protected function findModel($id)
    // {
    //     if (($model = Location::findOne($id)) !== null) {
    //         //        if (($model = Location::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }
    protected function findModel($id)
    {
        $location = Location::findOne($id);
        $query = Location::find()->where(['id' => $id]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andWhere(['division_id' => $location->division_id]);
        } else if (Account::getAdminDivisionID() == Division::DIVISION_VILLA) {
            $query->andWhere(['division_id' => Yii::$app->user->identity->division_id]);
        } else {
            $mainSectorId = Account::getAdminMainSectorId();
            $sectorIds = ArrayHelper::getColumn(MainSector::findOne($mainSectorId)->sectors, 'id');
            $query->andWhere([
                'division_id' => Yii::$app->user->identity->division_id,
                'sector_id' => $sectorIds,
            ]);
        }

        if (!empty($location->main_sector_id)) {
            $query->andWhere(['main_sector_id' => $location->main_sector_id]);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
