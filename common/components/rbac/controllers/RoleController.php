<?php

namespace common\components\rbac\controllers;

use common\components\rbac\models\Role;
use common\components\rbac\models\RoleSearch;
use common\config\includes\P;
use common\models\AccountType;
use common\models\Admin;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RoleController is controller for manager role
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class RoleController extends Controller
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
                        'allow' => P::c(P::ADMINS_ROLE_PAGE_VIEW),
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' =>  P::c(P::ADMINS_ROLE_PAGE_VIEW),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::ADMINS_ROLE_PAGE_VIEW),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::ADMINS_ROLE_PAGE_VIEW),
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Role models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Role model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Role(null);
        if ($model->load($request->post())) {

            $model->name = Inflector::slug($request->post()['Role']['name']);

            if ($model->save())
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Role model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($model->load($request->post())) {
            if ($model->save())
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing Role model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    protected function findModel($id)
    {
        $admin = Admin::findOne(Yii::$app->user->identity);
        if ($admin) {
            $accountType = AccountType::findOne(['name' => $id]);
            if ($accountType) {
                $childAdminTypes = AccountType::find()
                    ->where([
                        'or',
                        ['parent_id' => $admin->account->type0->id],
                        ['in', 'parent_id', AccountType::find()->select('id')->where(['parent_id' => $admin->account->type0->id])],

                    ])
                    ->all();
                $isAllowed = in_array($accountType->name, array_map(function ($child) {
                    return $child->name;
                }, $childAdminTypes));

                if ($admin->account->type0->parent_id === null || $isAllowed) {
                    if (($model = Role::find($id)) !== null) {
                        return $model;
                    } else {
                        throw new NotFoundHttpException(Yii::t('rbac', 'The requested page does not exist.'));
                    }
                }
            }
        }

        throw new NotFoundHttpException(Yii::t('rbac', 'The requested page does not exist.'));
    }
}
