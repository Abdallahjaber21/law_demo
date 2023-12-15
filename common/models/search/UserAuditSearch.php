<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserAudit;
use common\models\Admin;

/**
 * UserAuditSearch represents the model behind the search form about `common\models\UserAudit`.
 */
class UserAuditSearch extends UserAudit
{
    public $created_at_from;
    public $created_at_to;
    public $updated_at_from;
    public $updated_at_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'class_id', 'status', 'created_by',  'updated_by'], 'integer'],
            [['id', 'created_at', 'entity_row_id', 'action', 'updated_at', 'old_value', 'new_value', 'created_at_from', 'created_at_to', 'updated_at_to', 'updated_at_from'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */

    public function search($params)
    {
        $query = UserAudit::find();
        $classId = Yii::$app->request->get('class_id');
        $entityId = Yii::$app->request->get('entity_row_id');
        if ($classId !== null) {
            $this->class_id = $classId;
        }
        if ($entityId !== null) {
            $this->entity_row_id = $entityId;
        }
        $query = UserAudit::find();
        $permissionsConditions = [];
        if (Yii::$app->user->can('management_location_page_audit')) {
            $permissionsConditions[] = ['class_id' => UserAudit::CLASS_NAME_LOCATION];
        }

        if (Yii::$app->user->can('management_location-equipments_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_LOCATIONEQUIPMENT];
        }
        if (Yii::$app->user->can('management_equipment_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_EQUIPMENT];
        }
        if (Yii::$app->user->can('management_technician_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_TECHNICIAN];
        }
        if (Yii::$app->user->can('management_equipment-type_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_EQUIPMENTTYPE];
        }
        if (Yii::$app->user->can('management_segment-path_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_SEGMENTPATH];
        }
        if (Yii::$app->user->can('configurations_profession_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_PROFESSION];
        }
        if (Yii::$app->user->can('configurations_category_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_CATEGORY];
        }
        if (Yii::$app->user->can('configurations_main-sector_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_MAINSECTOR];
        }
        if (Yii::$app->user->can('admins_admin_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_ADMIN];
        }
        if (Yii::$app->user->can('configurations_sector_page_audit')) {
            $permissionsConditions[] = ['class_id' =>  UserAudit::CLASS_NAME_SECTOR];
        }
        if (!empty($permissionsConditions)) {
            $query->orWhere(array_merge(['or'], $permissionsConditions));
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'class_id' => $this->class_id,
            'entity_row_id' => $this->entity_row_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'old_value', $this->old_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', UserAudit::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', UserAudit::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        return $dataProvider;
    }
}
