<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\search\TechnicianLocationSearch;
use common\models\Technician;
use common\models\TechnicianLocation;
use common\models\TechnicianSector;
use common\models\users\Admin;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * TechnicianLocationController implements the CRUD actions for TechnicianLocation model.
 */
class TechnicianLocationController extends Controller
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
                        //'actions' => ['index', 'view', 'map', 'online', 'disabled', 'offline'],
                        'roles' => ['@'],
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
     * Lists all TechnicianLocation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TechnicianLocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMap()
    {
        //        $tid = \Yii::$app->getRequest()->post("technician_id");
        //        $locations = TechnicianLocation::find()
        //            ->filterWhere(['technician_id' => $tid])
        //            ->all();
        return $this->render("map", [
            //            'locations' => $locations
        ]);
    }

    public function actionOnline()
    {

        $locations = TechnicianLocation::find()
            ->joinWith(['technician', 'technician.technicianSectors'])
            ->with(['technician'])
            ->where([
                'AND',
                [Technician::tableName() . '.status' => Technician::STATUS_ENABLED],
                ['>=', Technician::tableName() . '.updated_at', gmdate("Y-m-d h:i:s", strtotime("-30 minutes"))]
            ])
            ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
            ->all();
        $technicians = ArrayHelper::getColumn($locations, 'technician');
        return $this->renderPartial("technicians-list", [
            'technicians' => $technicians,
            'title'       => 'Online Technicians',
            'type'        => 'online'
        ]);
    }


    public function actionDisabled()
    {

        $technicians = Technician::find()
            ->joinWith(['technicianSectors'])
            ->where([Technician::tableName() . '.status' => Technician::STATUS_DISABLED])
            ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
            ->all();
        return $this->renderPartial("technicians-list", [
            'technicians' => $technicians,
            'title'       => 'Disabled Technicians',
            'type'        => 'disabled'
        ]);
    }

    public function actionOffline()
    {

        $onlineIds = ArrayHelper::getColumn(TechnicianLocation::find()
            ->joinWith(['technician', 'technician.technicianSectors'])
            ->select([TechnicianLocation::tableName() . '.technician_id'])
            ->where([
                'AND',
                [Technician::tableName() . '.status' => Technician::STATUS_ENABLED],
                ['>=', Technician::tableName() . '.updated_at', gmdate("Y-m-d h:i:s", strtotime("-30 minutes"))]
            ])
            ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
            ->asArray()
            ->all(), 'technician_id', false);
        $technicians = Technician::find()
            ->joinWith(['technicianSectors'])
            ->with(['technicianLocations'])
            ->where([
                'AND',
                ['!=', Technician::tableName() . '.status', Technician::STATUS_DISABLED],
                ['NOT IN', Technician::tableName() . '.id', $onlineIds]
            ])
            ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
            ->all();
        return $this->renderPartial("technicians-list", [
            'technicians' => $technicians,
            'title'       => 'Offline Technicians',
            'type'        => 'offline'
        ]);
    }

    /**
     * Displays a single TechnicianLocation model.
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
     * Finds the TechnicianLocation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TechnicianLocation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TechnicianLocation::findOne($id)) !== null) {
            //        if (($model = TechnicianLocation::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new TechnicianLocation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TechnicianLocation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TechnicianLocation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TechnicianLocation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
