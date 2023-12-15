<?php

namespace common\components\rbac\controllers;

use common\components\rbac\models\AssignmentForm;
use common\components\rbac\models\AssignmentSearch;
use common\components\rbac\Module;
use common\config\includes\P;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;

/**
 * AssignmentController is controller for manager user assignment
 *
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class AssignmentController extends Controller
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
                        'allow' => P::c(P::ADMINS_ROLE_PAGE_VIEW) || P::c(P::ADMINS_ADMIN_PAGE),
                        'actions' => ['assignment'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * The current rbac module
     * @var Module $rbacModule
     */
    protected $rbacModule;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->rbacModule = Yii::$app->getModule('rbac');
    }

    /**
     * Show list of user for assignment
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AssignmentSearch;
        $dataProvider = $searchModel->search();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'idField' => $this->rbacModule->userModelIdField,
            'usernameField' => $this->rbacModule->userModelLoginField,
        ]);
    }

    /**
     * Assignment roles to user
     * @param mixed $id The user id
     * @return mixed
     */
    public function actionAssignment($id, $coming_from = null)
    {
        $model = call_user_func($this->rbacModule->userModelClassName . '::findOne', $id);

        $formModel = new AssignmentForm($id);
        $request = Yii::$app->request;
        if ($formModel->load($request->post()) && $formModel->save()) {

            if (!empty($coming_from) && $coming_from == 'admin') {
                return $this->redirect(['/admin/index']);
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('assignment', [
                'model' => $model,
                'formModel' => $formModel,
            ]);
        }
    }
}
