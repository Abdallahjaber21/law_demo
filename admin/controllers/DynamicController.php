<?php

namespace admin\controllers;

use yii\web\Response;
use common\config\includes\P;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\MainSector;
use yii\data\ActiveDataProvider;
use common\models\Sector;
use common\models\Account;

class DynamicController extends Controller
{
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => P::c(P::MANAGEMENT_DELETED_ENTITIES_PERMISSION_MOVE),
                        'actions' => ['update-status', 'move-child'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => 'true',
                        'actions' => ['update-parent-status', 'undelete'],
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



    public function actionMoveChild()
    {
        $parentClass = Yii::$app->request->get('parentClass');
        $childClass = Yii::$app->request->get('childClass');
        $paramsAttribute = Yii::$app->request->get('paramsAttribute');
        $parentId = Yii::$app->request->get('parentId');
        $childName = Yii::$app->request->get('childName');
        $parentName = Yii::$app->request->get('parentName');
        $deletedName = Yii::$app->request->get('deletedName');
        $urlPath = Yii::$app->request->get('urlPath');
        $parentRecord = $parentClass::findOne($parentId);
        if ($parentRecord == null) {
            throw new NotFoundHttpException('The parent record does not exist.');
        }
        if ($parentClass == 'common\models\AccountType') {
            $childRecords = $childClass::find()->joinWith('account')->where(['account.type' => $parentId])->andWhere(['=', 'status', $childClass::STATUS_ENABLED])->all();
        } else {
            $childRecords = $childClass::find()->where([$paramsAttribute => $parentId])->andWhere(['=', 'status', $childClass::STATUS_ENABLED])->all();
        }


        if (Yii::$app->request->isPost) {
            $selectedChildIds = Yii::$app->request->post('selection');
            $newParentId = Yii::$app->request->post('newParentId');
            $ischanged = false;
            $childMoved = false;
            $deleteparent = false;
            if (!empty($selectedChildIds)) {
                if (Yii::$app->request->post('deleteButton') !== null) {
                    foreach ($selectedChildIds as $childId) {
                        $childModel = $childClass::findOne($childId);
                        if ($childModel != null) {
                            $childModel->status = $childClass::STATUS_DELETED;
                            if ($parentClass == 'common\models\AccountType') {
                                $childModel->account_type = $parentId;
                            }
                            if ($childModel->save(false)) {

                                $ischanged = true;
                            }

                            $deleteparent = true;
                        }
                    }
                    if ($ischanged) {
                        Yii::$app->session->setFlash('error', 'Child has been deleted.');
                    }
                } elseif (Yii::$app->request->post('moveButton') !== null && $newParentId !== null) {

                    foreach ($selectedChildIds as $childId) {
                        if ($parentClass == 'common\models\AccountType') {

                            $childModel = $childClass::find()
                                ->joinWith('account')->where([$childClass::tableName() . '.id' => $childId])
                                ->one();
                        } else {
                            $childModel = $childClass::findOne($childId);
                        }
                        if ($childModel != null) {
                            if ($parentClass == 'common\models\AccountType') {
                                $childModel->account_type = $newParentId;
                                $childModel->account_type = $newParentId;
                            } else {
                                $childModel->$paramsAttribute = $newParentId;
                            }

                            $childModel->save(false);
                            //  if ($parentClass == 'common\models\AccountType') {
                            //   $account = Account::findOne($parentId);
                            // $account->type = $newParentId;
                            // $account->save(false);
                            // }
                            $deleteparent = true;
                            $childMoved = true;
                        }
                    }

                    if ($childMoved) {
                        Yii::$app->session->setFlash('error', 'Child has been moved.');
                    }
                }
                if ($deleteparent) {
                    if ($parentClass == 'common\models\AccountType') {
                        $hasEnabledChild = $childClass::find()->joinWith('account')
                            ->where(['account.type' => $parentId])
                            ->andWhere(['=', 'status', $childClass::STATUS_ENABLED])
                            ->exists();
                    } else {
                        $hasEnabledChild = $childClass::find()
                            ->where([$paramsAttribute => $parentId])
                            ->andWhere(['=', 'status', $childClass::STATUS_ENABLED])
                            ->exists();
                    }
                    if (!$hasEnabledChild) {
                        if ($parentRecord) {
                            $parentRecord->status = $parentClass::STATUS_DELETED;
                            $parentRecord->save(false);
                            Yii::$app->session->setFlash('error', 'The ' . $parentName . ' has been deleted.');
                            return $this->redirect($urlPath);
                        }
                    }
                }
            }
        }
        if ($parentClass == 'common\models\AccountType') {
            $query = $childClass::find()->joinWith('account')
                ->where(['account.type' => $parentId])->andWhere(['!=', 'status', $childClass::STATUS_DELETED]);
            //print_r($query->createcommand()->rawSql);
            //exit;
        } else {
            $query = $childClass::find()->where([$paramsAttribute => $parentId])->andWhere(['!=', 'status', $childClass::STATUS_DELETED]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $account = Account::findOne($parentId);


        return $this->render('moved_popup', [
            'dataProvider' => $dataProvider,
            'childRecords' => $childRecords,
            'parentClass' => $parentClass,
            'childClass' => $childClass,
            'paramsAttribute' => $paramsAttribute,
            'parentId' => $parentId,
            'childName' => $childName,
            'parentName' => $parentName,
            'deletedName' => $deletedName,
            'urlPath' => $urlPath,
        ]);
    }


    public function actionUpdateParentStatus()
    {
        $postData = Yii::$app->request->post();
        $response = [];

        if (isset($postData['parentId']) && isset($postData['parentClass']) && isset($postData['childClass']) && isset($postData['paramsAttribute'])) {

            $model = $postData['parentClass']::findOne($postData['parentId']);
            if ($model != null) {
                if ($postData['parentClass'] == 'common\models\AccountType') {

                    $allchild = $postData['childClass']::find()
                        ->joinWith('account')->where(['account.type' => $model->id])
                        ->andWhere(['=', 'status', $postData['childClass']::STATUS_ENABLED])
                        ->all();
                } else {
                    $allchild = $postData['childClass']::find()
                        ->where([$postData['paramsAttribute'] => $model->id])
                        ->andWhere(['=', 'status', $postData['childClass']::STATUS_ENABLED])
                        ->all();
                }
                if (!Yii::$app->user->can('management_deleted-entities_permission_move')) {
                    $model->status = $postData['parentClass']::STATUS_DELETED;
                    if ($model->save(false)) {
                        if (!empty($postData['childClass'])) {
                            $childStatus = $postData['childClass']::STATUS_ENABLED;
                            if ($postData['parentClass'] == 'common\models\AccountType') {
                                $childModels = $postData['childClass']::find()
                                    ->joinWith('account')->where(['account.type' => $model->id])
                                    ->andWhere(['=', 'status', $childStatus])
                                    ->all();
                            } else {
                                $childModels = $postData['childClass']::find()
                                    ->where([$postData['paramsAttribute'] => $model->id])
                                    ->andWhere(['=', 'status', $childStatus])
                                    ->all();
                            }

                            foreach ($childModels as $child) {
                                $child->status = $postData['childClass']::STATUS_DELETED;
                                $child->save();
                            }
                        }
                        Yii::$app->session->setFlash('warning', 'This item , as well as their ' . $postData['childName'] . ', has been successfully deleted.');
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                } else {
                    if ($postData['parentClass'] == 'common\models\AccountType') {

                        $parentchild = $postData['childClass']::find()
                            ->joinWith('account')->where(['account.type' => $model->id])->count();
                        $hasEnabledChild = $postData['childClass']::find()
                            ->joinWith('account')->where(['account.type' => $model->id])
                            ->andWhere(['status' => $postData['childClass']::STATUS_DELETED])
                            ->count();
                    } else {
                        $parentchild = $postData['childClass']::find()
                            ->where([$postData['paramsAttribute'] => $postData['parentId']])->count();
                        $hasEnabledChild = $postData['childClass']::find()
                            ->where([$postData['paramsAttribute'] => $postData['parentId']])
                            ->andWhere(['status' => $postData['childClass']::STATUS_DELETED])
                            ->count();
                    }
                    if ($parentchild == $hasEnabledChild) {
                        $model->status = $postData['parentClass']::STATUS_DELETED;
                        if ($model->save(false)) {
                            Yii::$app->session->setFlash('warning', 'This item has been deleted.');
                            return $this->redirect(Yii::$app->request->referrer);
                        }
                    }
                }
            } else {

                $response['message'] = 'Parent not found.';
            }
            $response['message'] = 'Success';
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionUndelete($parentClass = null, $parentId = null, $currentClass, $currentId, $childClass, $paramsAttribute)
    {

        $model = $currentClass::findOne($currentId);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Parent item not found.');
            return $this->redirect(Yii::$app->request->referrer);
        }
        if ($parentClass != '') {
            $parent = $parentClass::findOne($parentId);
        }
        if ($parentClass == '' || $parent->status != $parentClass::STATUS_DELETED) {
            if ($model->status == $currentClass::STATUS_DELETED) {
                $model->status = $currentClass::STATUS_ENABLED;

                if ($model->save()) {
                    $childModels = [];

                    if ($currentClass == 'common\models\AccountType') {
                        $childModels = $childClass::find()
                            ->joinWith('account')
                            ->where(['account.type' => $currentId])
                            ->all();
                    } else {
                        $childModels = $childClass::find()
                            ->where([$paramsAttribute => $currentId])
                            ->all();
                    }

                    foreach ($childModels as $childModel) {
                        if ($currentClass == 'common\models\AccountType') {
                            $childModel->account_type = $currentId;
                        }
                        $childModel->status = $childClass::STATUS_ENABLED;
                        $childModel->save();
                    }

                    Yii::$app->session->setFlash('success', 'Item has been undeleted.');
                } else {
                    Yii::$app->session->setFlash('error', 'Item cannot be undeleted.');
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'Item cannot be undeleted, the parent has status deleted');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}
