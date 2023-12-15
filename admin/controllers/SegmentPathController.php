<?php

namespace admin\controllers;

use admin\models\Model;
use common\config\includes\P;
use Yii;
use common\models\SegmentPath;
use common\models\Technician;
use common\models\search\SegmentPathSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;


/**
 * SegmentPathController implements the CRUD actions for SegmentPath model.
 */
class SegmentPathController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_NEW),
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
     * Lists all SegmentPath models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SegmentPathSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SegmentPath model.
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
     * Creates a new SegmentPath model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $segment_pathes_model = [new SegmentPath];

        if ($this->request->post()) {

            $post_vars = $this->request->post()['SegmentPath'];
            $sectors = $post_vars['sector_id'];

            if (!empty($sectors)) {
                foreach ($sectors as $key => $sector) {
                    $model = new SegmentPath();
                    $model->name = $post_vars['name'];
                    $model->code = $post_vars['code'];
                    $model->description = $post_vars['description'];
                    $model->sector_id = $sector;
                    $model->status = $post_vars['status'];
                    // $model->load(Yii::$app->request->post());
                    $segment_pathes_model = Model::createMultiple(SegmentPath::classname());
                    Model::loadMultiple($segment_pathes_model, Yii::$app->request->post());

                    // Create The Json Segment Here
                    $model->value = SegmentPath::GetJsonSegment($segment_pathes_model);

                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', $model->name . ' Created Successfully');
                    }
                }
            }

            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => new SegmentPath(),
            'segment_pathes_model' => (empty($segment_pathes_model)) ? [new SegmentPath] : $segment_pathes_model,
        ]);
    }

    /**
     * Updates an existing SegmentPath model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $segment_pathes_model = SegmentPath::getLayersArrayModels($model->value);

        // new code
        if ($this->request->post()) {

            $post_vars = $this->request->post()['SegmentPath'];

            $sectors = $post_vars['sector_id'];

            if (!empty($sectors)) {
                foreach ($sectors as $key => $sector) {

                    if ($model->sector_id != $sector) {
                        $model = new SegmentPath();
                        $model->sector_id = $sector;
                    }

                    $model->name = $post_vars['name'];
                    $model->description = $post_vars['description'];
                    // $model->load(Yii::$app->request->post());
                    $segment_pathes_model = Model::createMultiple(SegmentPath::classname());
                    Model::loadMultiple($segment_pathes_model, Yii::$app->request->post());

                    // Create The Json Segment Here
                    $model->value = SegmentPath::GetJsonSegment($segment_pathes_model);
                    $model->status = $post_vars['status'];
                    $model->code = $post_vars['code'];

                    if ($model->save()) {
                        Yii::$app->session->setFlash('warning', $model->name . ' Updated Successfully');
                    }
                }
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'segment_pathes_model' => $segment_pathes_model
        ]);
    }

    /**
     * Deletes an existing SegmentPath model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        if ($model->status == SegmentPath::STATUS_ENABLED) {
            $model->status = SegmentPath::STATUS_DELETED;
        } else {
            $model->status = SegmentPath::STATUS_ENABLED;
        }
        if ($model->save()) {
            if ($model->status == SegmentPath::STATUS_ENABLED) {

                Yii::$app->session->addFlash("warning", "Segment Path  " . $name . " has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Segment Path  " . $name . " has been deleted");
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the SegmentPath model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SegmentPath the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $segmentPath = SegmentPath::findOne($id);
        $query = SegmentPath::find()->where(['id' => $id]);
        $technicianSectors = Technician::getTechnicianSectorsOptions();
        $sectorIds = ArrayHelper::getColumn($technicianSectors, 'id');
        $query->andFilterWhere(['IN', 'segment_path.sector_id', $sectorIds]);
        if (isset($this->division_id) && !empty($segmentPath->division_id)) {
            $query->andFilterWhere(['=', MainSector::tableName() . '.division_id', $this->division_id]);
        }
        if (($model = $query->one()) !== null) {
            //        if (($model = SegmentPath::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
