<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Location;
use common\models\LocationEquipments;
use Yii;
use common\models\VillaPpmTemplates;
use common\models\search\VillaPpmTemplatesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\filters\AccessControl;


/**
 * VillaPpmTemplatesController implements the CRUD actions for VillaPpmTemplates model.
 */
class VillaPpmTemplatesController extends Controller
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
                        'allow' => P::c(P::PPM_VILLA_PPM_TEMPLATES_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_VILLA_PPM_TEMPLATES_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_VILLA_PPM_TEMPLATES_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_VILLA_PPM_TEMPLATES_NEW),
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
     * Lists all VillaPpmTemplates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VillaPpmTemplatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VillaPpmTemplates model.
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
     * Creates a new VillaPpmTemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VillaPpmTemplates();

        if ($model->load(Yii::$app->request->post())) {

            $post = Yii::$app->request->post('VillaPpmTemplates');

            $model->location_id = @Location::find()->where(['code' => @$post['location_id']])->one()->id;

            $equipment_code = @$post['asset_id'];

            $model->asset_id = $model->location_id ? @LocationEquipments::find()->where(['code' => $equipment_code, 'location_id' => $model->location_id])->one()->id : null;

            $model->team_members = !empty($post['team_members']) ? implode(',', @$post['team_members']) : null;
            $model->tasks = !empty($post['tasks']) ? implode(',', @$post['tasks']) : null;

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing VillaPpmTemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->asset_id = @$model->asset->code;
        $model->location_id = @$model->location->code;

        if ($model->load(Yii::$app->request->post())) {

            $post = Yii::$app->request->post('VillaPpmTemplates');

            $model->location_id = @Location::find()->where(['code' => @$post['location_id']])->one()->id;

            $equipment_code = @$post['asset_id'];

            $model->asset_id = $model->location_id ? @LocationEquipments::find()->where(['code' => $equipment_code, 'location_id' => $model->location_id])->one()->id : null;

            $model->team_members = !empty($post['team_members']) ? implode(',', @$post['team_members']) : null;
            $model->tasks = !empty($post['tasks']) ? implode(',', @$post['tasks']) : null;
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                print_r($model->getErrors());
                exit;
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing VillaPpmTemplates model.
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
     * Finds the VillaPpmTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VillaPpmTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VillaPpmTemplates::findOne($id)) !== null) {
            //        if (($model = VillaPpmTemplates::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
