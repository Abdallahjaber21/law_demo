<?php

namespace admin\controllers;

use common\components\notification\Notification;
use common\config\includes\P;
use common\models\Assignee;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\RepairRequest;
use common\models\search\RepairRequestSearch;
use common\models\SegmentPath;
use common\models\Technician;
use common\models\users\Admin;
use Imagick;
use Yii;
use yii\base\DynamicModel;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * RepairRequestController implements the CRUD actions for RepairRequest model.
 */
class ExternalWorkOrderController extends Controller
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
                        'allow'   => P::c(P::REPAIR_EXTERNAL_WORK_ORDER_PAGE_VIEW),
                        'actions' => ['index'],
                        'roles'   => ['@'],

                    ],
                    [
                        'allow'   => P::c(P::REPAIR_EXTERNAL_WORK_ORDER_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles'   => ['@'],
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
     * Lists all RepairRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->division_id == Division::DIVISION_VILLA || Yii::$app->user->identity->division_id == '') {
            $searchModel = new RepairRequestSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            // Additional query filters as needed
            $dataProvider->query->andWhere([
                'repair_request.division_id' => Division::DIVISION_MALL,
                'technician_from_another_division' => 1,
            ])->andWhere(['not in', 'repair_request.status', ['STATUS_CANCELLED', 'STATUS_Completed', 'STATUS_DRAFT']]);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }






    /**
     * Finds the RepairRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RepairRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (Yii::$app->user->identity->division_id == Division::DIVISION_VILLA || Yii::$app->user->identity->division_id == '') {
            if (($model = RepairRequest::findOne($id)) !== null) {
                //        if (($model = RepairRequest::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            $selectedTechnicians = Yii::$app->request->post('technician_id');
            $assigneesToDelete = Assignee::find()
                ->joinWith('user')
                ->where([
                    'assignee.repair_request_id' => $model->id,
                    'technician.division_id' => Division::DIVISION_VILLA
                ])
                ->all();

            foreach ($assigneesToDelete as $assignee) {
                $assignee->delete();
            }
            if (is_array($selectedTechnicians)) {
                foreach ($selectedTechnicians as $technicianId) {
                    $assignee = Assignee::find()
                        ->where(['repair_request_id' => $model->id, 'user_id' => $technicianId])
                        ->one();
                    if ($assignee) {
                        $assignee->status = Assignee::STATUS_ASSIGNED;
                        $assignee->datetime = $model->scheduled_at;
                    } else {
                        $assignee = new Assignee();
                        $assignee->repair_request_id = $model->id;
                        $assignee->user_id = $technicianId;
                        $assignee->datetime = $model->scheduled_at;
                        $assignee->status = Assignee::STATUS_ASSIGNED;
                    }
                    $assignee->save();
                }
                Yii::$app->getSession()->setFlash('success', 'Assignees have been updated for the repair request.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
}
