<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\CustomerUser;
use common\models\search\UserSearch;
use common\models\User;
use common\models\UserLocation;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                        'allow'   => P::c(P::MANAGEMENT_USER_PAGE),
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionRemoveFromLocation($user_id, $location_id, $_ref = 'user')
    {
        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['user_id' => $user_id]
            ])->one();

        if (!empty($userLocation)) {
            $userLocation->delete();
            Yii::$app->session->addFlash("success", "User removed from location");
        }

        if ($_ref === "user") {
            return $this->redirect(['user/view', 'id' => $user_id]);
        }
        return $this->redirect(['location/view', 'id' => $location_id]);
    }

    public function actionChangeRole($user_id, $location_id, $_ref = 'user', $role = UserLocation::ROLE_RESIDENT)
    {
        $userLocation = UserLocation::find()
            ->where([
                'AND',
                ['location_id' => $location_id],
                ['user_id' => $user_id]
            ])->one();

        if (!empty($userLocation)) {
            $userLocation->role = $role;
            $userLocation->save();
            Yii::$app->session->addFlash("success", "User role updated");
        }

        if ($_ref === "user") {
            return $this->redirect(['user/view', 'id' => $user_id]);
        }
        return $this->redirect(['location/view', 'id' => $location_id]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            //        if (($model = User::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = Yii::$app->request->post("User");
            UserLocation::deleteAll(['user_id' => $model->id]);
            if (!empty($user['userLocations'])) {
                $userLocations = $user['userLocations'];
                if (!empty($userLocations)) {
                    foreach ($userLocations as $index => $userLocation) {
                        (new UserLocation([
                            'location_id' => $userLocation,
                            'user_id'     => $model->id,
                            'role'        => UserLocation::ROLE_RESIDENT,
                            'status'      => UserLocation::STATUS_ENABLED,
                        ]))->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = Yii::$app->request->post("User");
            UserLocation::deleteAll(['user_id' => $model->id]);
            if (!empty($user['userLocations'])) {
                $userLocations = $user['userLocations'];
                if (!empty($userLocations)) {
                    foreach ($userLocations as $index => $userLocation) {
                        (new UserLocation([
                            'location_id' => $userLocation,
                            'user_id'     => $model->id,
                            'role'        => UserLocation::ROLE_RESIDENT,
                            'status'      => UserLocation::STATUS_ENABLED,
                        ]))->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
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
