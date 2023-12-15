<?php

namespace admin\controllers;

use common\components\rbac\controllers\AssignmentController;
use common\components\rbac\models\AssignmentForm;
use common\config\includes\P;
use common\data\Countries;
use common\models\Account;
use common\models\AccountType;
use Yii;
use common\models\Admin;
use common\models\search\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\users\AbstractAccount;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends Controller
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
                        'allow' => (P::c(P::ADMINS_ADMIN_PAGE_VIEW)),
                        'actions' => ['index', 'delete-picture'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => (P::c(P::ADMINS_ADMIN_PAGE_VIEW)),
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => (P::c(P::ADMINS_ADMIN_PAGE_UPDATE)),
                        'actions' => ['update', 'delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => (P::c(P::ADMINS_ADMIN_PAGE_NEW)),
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
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
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
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();

        $model->scenario = Admin::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post())) {
            if (Account::ValidateNumber($model->phone_number, $model->country)) {
                if ($model->save()) {
                    if (!empty($model->account)) {
                        Yii::$app->getSession()->addFlash("success", $model->name . " created successfully");

                        $account_type_model = AccountType::findOne($model->account_type);

                        $auth = Yii::$app->authManager;
                        $role = $auth->getRole($account_type_model->name);
                        $auth->assign($role, $model->id);
                        Yii::$app->session->setFlash('warning', $account_type_model->name . ' assigned successfully to: ' . $model->name);
                    }
                } else {
                    return $this->render('_form', [
                        'model' => $model
                    ]);
                }
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->addFlash("error", $model->phone_number . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->phone_number)));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_type = $model->account->type;

        if ($model->load(Yii::$app->request->post())) {

            $account_type_id = Yii::$app->request->post()['Admin']['account_type'];
            $model->account_type = $account_type_id;

            if (Account::ValidateNumber($model->phone_number, $model->country)) {
                if ($model->save()) {
                    if ($old_type != $model->account_type) {
                        $account = Account::findOne($model->id);
                        $account->type = $model->account_type;
                        if ($account->save()) {
                            $account_type_model = AccountType::findOne($model->account_type);

                            $roles_model = new AssignmentForm($model->id);
                            $roles_model->roles = [$account_type_model->name];
                            $roles_model->save();
                            Yii::$app->session->setFlash('warning', $account_type_model->name . ' assigned successfully to: ' . $model->name);
                        }
                    }
                    Yii::$app->getSession()->addFlash("success", "Admin: " . $model->name . " updated successfully");
                    return $this->redirect(['index']);
                } else {
                    return $this->render('_form', [
                        'model' => $model
                    ]);
                }
                return $this->redirect(['update', 'id' => $id]);
            } else {
                Yii::$app->getSession()->addFlash("error", $model->phone_number . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->phone_number)));
                return $this->redirect(['update', 'id' => $id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        $model->account_type = $model->account->type;
        $accountType = @AccountType::find()->where(['id' => $model->account->type])->one();
        $accountTypestatus = @$accountType->status;

        if ($accountTypestatus != AccountType::STATUS_DELETED) {
            if ($model->status == Admin::STATUS_ENABLED) {
                $model->status = Admin::STATUS_DELETED;
            } else {
                $model->status = Admin::STATUS_ENABLED;
            }
            $model->save();
            if ($model->status == Admin::STATUS_ENABLED) {

                Yii::$app->session->addFlash("success", "Admin " . $name . " has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Admin " . $name . " has been deleted");
            }
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->addFlash("warning", "You need to undelete the account type: " . $accountType->name . ' before deleting ' . $name);
            return $this->redirect(['index']);
        }
    }
    public function actionDeletePicture($id, $coming_from = null)
    {
        $model = Admin::findOne($id);

        Admin::updateAll(['image' => null], ['id' => $model->id]);
        $model->cleanFiles();

        if ($model->save(false)) {
            Yii::$app->session->setFlash('error', 'Profile Image Deleted');
        }

        if (!empty($coming_from) && $coming_from == 'site') {
            return $this->redirect(['site/profile', 'id' => $id]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Admin::find()->innerJoinWith('account')->joinWith('division')->joinWith('mainSector')->innerJoin('account_type', 'account_type.id = account.type');

        $user = Admin::findOne($id);
        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere(['admin.division_id' => $user->division_id]);
            $query->andFilterWhere(['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')]);
        } else {
            $query->andFilterWhere(
                [
                    'AND',
                    ['admin.division_id' => Account::getAdminAccountTypeDivisionModel()->id],
                    ['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')]
                ]
            );
        }

        if (isset($user->superadmin_division_id) && !empty($user->superadmin_division_id)) {
            $query->andFilterWhere(['=', Division::tableName() . '.id', $this->superadmin_division_id]);
        }

        if (isset($user->main_sector_id) && !empty($user->main_sector_id)) {
            $query->andFilterWhere(['=', 'admin.main_sector_id', $user->main_sector_id]);
        }

        $query->andFilterWhere(['admin.id' => $id]);

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
