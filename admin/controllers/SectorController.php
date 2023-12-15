<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\Sector;
use common\models\search\SectorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\MainSector;
use yii\filters\AccessControl;
use common\models\Account;
use common\models\Division;


/**
 * SectorController implements the CRUD actions for Sector model.
 */
class SectorController extends Controller
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
                        'allow' => P::c(P::CONFIGURATIONS_SECTOR_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_SECTOR_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_SECTOR_PAGE_VIEW),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_SECTOR_PAGE_NEW),
                        'actions' => ['create'],
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
     * Lists all Sector models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SectorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sector model.
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
     * Creates a new Sector model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new Sector();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("success", "Sector: " . $model->name . " is created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'main_sector_id' => $id,
            ]);
        }
    }

    /**
     * Updates an existing Sector model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("warning", "Sector: " . $model->name . " is updated successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Sector model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        $mainsector = MainSector::find()->where(['id' => $model->main_sector_id])->one();
        $mainsectorstatus = $mainsector->status;
        if ($mainsectorstatus != MainSector::STATUS_DELETED) {
            if ($model->status == Sector::STATUS_ENABLED) {
                $model->status = Sector::STATUS_DELETED;
            } else {
                $model->status = Sector::STATUS_ENABLED;
            }
            $model->save();
            if ($model->status == Sector::STATUS_ENABLED) {

                Yii::$app->session->addFlash("success", "Sector " . $name . " has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Sector " . $name . " has been deleted");
            }
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->addFlash("warning", "The main sector of this sector has been deleted, and it cannot be undeleted.");
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Sector model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sector the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Sector::find()->joinWith('mainSector')->where(['sector.id' => $id]);
        $sector = Sector::findOne($id);
        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => $sector->main_sector_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' =>  Yii::$app->user->identity->division_id]);
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => $sector->main_sector_id]);
        } else {
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => Yii::$app->user->identity->main_sector_id]);
        }

        if (($model = $query->one()) !== null) {
            //        if (($model = Sector::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
