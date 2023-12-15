<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\ProfessionCategory;
use common\models\search\ProfessionCategory as ProfessionCategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;


/**
 * ProfessionCategoryController implements the CRUD actions for ProfessionCategory model.
 */
class ProfessionCategoryController extends Controller
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
                        'allow' => Yii::$app->getUser()->can("developer") ||  P::c(P::CONFIGURATIONS_CATEGORY_PAGE_PROFESSIONS) || P::c(P::CONFIGURATIONS_PROFESSION_PAGE_CATEGORIES),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") || Yii::$app->getUser()->can("admin"),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") || Yii::$app->getUser()->can("admin"),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") || Yii::$app->getUser()->can("admin"),
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
     * Lists all ProfessionCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProfessionCategorySearch();
        if (Yii::$app->request->get('cat_id')) {
            $searchModel->cat_id = Yii::$app->request->get('cat_id');
        }
        if (Yii::$app->request->get('prof_id')) {
            $searchModel->prof_id = Yii::$app->request->get('prof_id');
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProfessionCategory model.
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
     * Creates a new ProfessionCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProfessionCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProfessionCategory model.
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
     * Deletes an existing ProfessionCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProfessionCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProfessionCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProfessionCategory::findOne($id)) !== null) {
            //        if (($model = ProfessionCategory::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
