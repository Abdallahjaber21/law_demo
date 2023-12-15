<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\MaintenanceTask;
use common\models\MaintenanceTaskGroup;
use common\models\search\MaintenanceTaskGroupSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;;


/**
 * MaintenanceTaskGroupController implements the CRUD actions for MaintenanceTaskGroup model.
 */
class MaintenanceTaskGroupController extends Controller
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
                        'allow' => P::c(P::DEVELOPER),
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
     * Lists all MaintenanceTaskGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaintenanceTaskGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all MaintenanceTaskGroup models.
     * @return mixed
     */
    public function actionManage()
    {
        if (Yii::$app->getRequest()->isPost) {
            $taskgroupinputs = \Yii::$app->getRequest()->post("outer-list");
            foreach ($taskgroupinputs as $index => $taskgroupinput) {
                $maintenanceTaskGroup = new MaintenanceTaskGroup();
                $maintenanceTaskGroup->equipment_type = $taskgroupinput['equipment_type'];
                $maintenanceTaskGroup->code = $taskgroupinput['group_code'];
                $maintenanceTaskGroup->name = $taskgroupinput['group_name'];
                $maintenanceTaskGroup->group_order = $taskgroupinput['group_order'];
                $maintenanceTaskGroup->status = MaintenanceTaskGroup::STATUS_ENABLED;
                if ($maintenanceTaskGroup->save()) {
                    $tasksinputs = $taskgroupinput['inner-list'];
                    foreach ($tasksinputs as $index => $taskinput) {
                        $maintenanceTask = new MaintenanceTask();
                        $maintenanceTask->maintenance_task_group_id = $maintenanceTaskGroup->id;
                        $maintenanceTask->code = $taskinput['task_code'];
                        $maintenanceTask->name = $taskinput['task_name'];
                        $maintenanceTask->status = MaintenanceTask::STATUS_ENABLED;
                        //$maintenanceTask->duration = $taskinput['task_duration'];
                        for ($i = 1; $i <= 12; $i++) {
                            $varA = "m_{$i}_a";
                            $varB = "m_{$i}_b";
                            $maintenanceTask->{$varA} = !empty($taskinput[$varA]);
                            $maintenanceTask->{$varB} = !empty($taskinput[$varB]);
                            $maintenanceTask->save();
                        }
                    }
                }
            }
            return $this->redirect("index");
        }
        return $this->render('manage', []);
    }

    /**
     * Displays a single MaintenanceTaskGroup model.
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
     * Finds the MaintenanceTaskGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaintenanceTaskGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaintenanceTaskGroup::findOne($id)) !== null) {
            //        if (($model = MaintenanceTaskGroup::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new MaintenanceTaskGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaintenanceTaskGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MaintenanceTaskGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $maintenanceTaskGroup = $this->findModel($id);

        if (Yii::$app->getRequest()->isPost) {
            $taskgroupinputs = \Yii::$app->getRequest()->post("outer-list");
            foreach ($taskgroupinputs as $index => $taskgroupinput) {
                //$maintenanceTaskGroup = new MaintenanceTaskGroup();
                $maintenanceTaskGroup->equipment_type = $taskgroupinput['equipment_type'];
                $maintenanceTaskGroup->code = $taskgroupinput['group_code'];
                $maintenanceTaskGroup->name = $taskgroupinput['group_name'];
                $maintenanceTaskGroup->group_order = $taskgroupinput['group_order'];
                $maintenanceTaskGroup->status = MaintenanceTaskGroup::STATUS_ENABLED;
                if ($maintenanceTaskGroup->save()) {
                    $tasksinputs = $taskgroupinput['inner-list'];
                    $updatedIds = [];
                    foreach ($tasksinputs as $index => $taskinput) {
                        $maintenanceTask = new MaintenanceTask();
                        if (!empty($taskinput['task_id'])) {
                            $updatedIds[] = $taskinput['task_id'];
                            $maintenanceTask = MaintenanceTask::findOne($taskinput['task_id']);
                        }
                        $maintenanceTask->maintenance_task_group_id = $maintenanceTaskGroup->id;
                        $maintenanceTask->code = $taskinput['task_code'];
                        $maintenanceTask->name = $taskinput['task_name'];
                        $maintenanceTask->status = MaintenanceTask::STATUS_ENABLED;
                        //$maintenanceTask->duration = $taskinput['task_duration'];
                        for ($i = 1; $i <= 12; $i++) {
                            $varA = "m_{$i}_a";
                            $varB = "m_{$i}_b";
                            $maintenanceTask->{$varA} = !empty($taskinput[$varA]);
                            $maintenanceTask->{$varB} = !empty($taskinput[$varB]);
                        }
                        $maintenanceTask->save();
                        if (empty($taskinput['task_id'])) {
                            $updatedIds[] = $maintenanceTask->id;
                        }
                    }
                    MaintenanceTask::deleteAll([
                        'AND',
                        ['maintenance_task_group_id' => $maintenanceTaskGroup->id],
                        ['NOT IN', 'id', $updatedIds]
                    ]);
                } else {
                    Yii::$app->getSession()->addFlash(
                        "danger",
                        implode("<br/>", $maintenanceTaskGroup->getFirstErrors())
                    );
                }
            }
            return $this->redirect(["index"]);
            //            echo "<pre>";
            //            print_r(\Yii::$app->getRequest()->post());
            //            echo "</pre>";
            //            exit();
        }
        return $this->render('update', [
            'model' => $maintenanceTaskGroup,
        ]);
    }

    /**
     * Deletes an existing MaintenanceTaskGroup model.
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
