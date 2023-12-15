<?php

namespace admin\controllers;

use common\components\rbac\models\AssignmentForm;
use common\components\rbac\models\AuthItem;
use common\config\includes\P;
use Yii;
use common\models\AccountType;
use common\models\Account;
use common\models\AuthItem as ModelsAuthItem;
use common\models\search\AccountTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\helpers\Inflector;

/**
 * AccountTypeController implements the CRUD actions for AccountType model.
 */
class AccountTypeController extends Controller
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
                        'allow' => P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_NEW),
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
     * Lists all AccountType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountType model.
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
     * Creates a new AccountType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccountType();

        if ($model->load(Yii::$app->request->post())) {

            $is_backend_user = @Yii::$app->request->post('AccountType')['for_backend'];

            $model->name = strtolower(Inflector::slug(Yii::$app->request->post()['AccountType']['label']));

            if ($model->save()) {

                if ($is_backend_user == true) {

                    $auth_item_model = ModelsAuthItem::find()->where(['name'  => $model->name])->one();

                    $auth = Yii::$app->authManager;

                    if (empty($auth_item_model)) {
                        // $role = $auth->getRole($model->name);

                        $role = $auth->createRole($model->name);
                        $role->description = $model->label;

                        $auth->add($role);
                    }


                    $role = $auth->getRole($model->name);

                    $model->role_id = $role->name;
                    $model->save();
                }

                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AccountType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $is_backend_user = @Yii::$app->request->post('AccountType')['for_backend'];
            $model->name = strtolower(Inflector::slug(Yii::$app->request->post()['AccountType']['label']));

            if ($model->save()) {

                // if ($is_backend_user) {
                //     $auth = Yii::$app->authManager;
                //     $role = $auth->getRole($model->role_id);

                //     $role->name = $model->name;

                //     $auth->update($model->role_id, $role);
                //     $model->save();
                // }

                return $this->redirect(['index']);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AccountType model.
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
     * Finds the AccountType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {


        if (
            ($model = AccountType::find()->where(['id' => $id])->andFilterWhere(
                [
                    'OR',
                    ['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')],
                    ['=', 'for_backend', false]
                ]
            )->one()) !== null
        ) {

            //        if (($model = AccountType::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
