<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Account;
use common\models\Division;
use Yii;
use common\models\MainSector;
use common\models\search\MainSectorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * MainSectorController implements the CRUD actions for MainSector model.
 */
class MainSectorController extends Controller
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
                        'allow' => P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_UPDATE),
                        'actions' => ['update', 'undelete'],
                        'roles' => ['@'],
                    ],
                    // [
                    //     'allow' => P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_VIEW),
                    //     'actions' => ['delete'],
                    //     'roles' => ['@'],
                    // ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_NEW),
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
     * Lists all MainSector models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MainSectorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MainSector model.
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
     * Creates a new MainSector model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MainSector();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("success", "Main Sector: " . $model->name . " is created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MainSector model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("warning", "Main Sector: " . $model->name . " is updated successfully");

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MainSector model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $mainsector = MainSector::findOne($id);
        $name = $mainsector->name;
        $this->findModel($id)->delete();
        Yii::$app->session->addFlash("danger", "Main Sector " . $name . " is deleted");
        return $this->redirect(['index']);
    }

    /**
     * Finds the MainSector model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MainSector the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $mainSector = MainSector::findOne($id);
        $query = MainSector::find()->where(['id' => $id]);
        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' => $mainSector->division_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' =>  Yii::$app->user->identity->division_id]);
        } else {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' => Yii::$app->user->identity->division_id]);
            $query->andFilterWhere([MainSector::tableName() . '.id' => Account::getAdminMainSectorId()]);
        }
        if (($model = $query->one()) !== null) {
            //        if (($model = MainSector::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
