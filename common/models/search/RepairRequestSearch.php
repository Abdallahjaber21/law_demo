<?php

namespace common\models\search;

use common\models\Category;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\Location;
use common\models\LocationEquipments;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RepairRequest;

/**
 * RepairRequestSearch represents the model behind the search form about `common\models\RepairRequest`.
 */
class RepairRequestSearch extends RepairRequest
{

    public $meter_value;
    public $meter_type;
    public $days_Overdue;
    public $formNameParam = "RepairRequestSearch";

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['technician_id', 'service_type', 'status', 'created_by', 'updated_by', 'problem', 'completed_by', 'owner_id', 'team_leader_id', 'urgent_status', 'division_id', 'project_id', 'category_id'], 'integer'],
            [['id', 'requested_at', 'scheduled_at', 'informed_at', 'arrived_at', 'departed_at', 'created_at', 'updated_at', 'assigned_at', 'customer_signature', 'random_token', 'completed_at', 'note', 'technician_signature', 'reported_by_name', 'reported_by_phone', 'notification_id', 'description', 'repair_request_path', 'service_note', 'meter_value', 'location_id', 'equipment_id', 'meter_type', 'days_Overdue', 'labor_charge'], 'safe'],
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

        $query = RepairRequest::find()
            ->joinWith(['location', 'equipment', 'equipment.equipment', 'equipment.equipment.equipmentType', 'equipment.equipment.category']);

        // add conditions that should always apply here

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
        // $query->andWhere([RepairRequest::tableName() . '.status' => RepairRequest::STATUS_COMPLETED]);

        // grid filtering conditions

        $query->andFilterWhere([
            RepairRequest::tableName() . '.id' => $this->id,
            RepairRequest::tableName() . '.technician_id' => $this->technician_id,
            // RepairRequest::tableName() . '.equipment_id' => $this->equipment_id,
            RepairRequest::tableName() . '.service_type' => $this->service_type,
            RepairRequest::tableName() . '.status' => $this->status,
            RepairRequest::tableName() . '.created_by' => $this->created_by,
            RepairRequest::tableName() . '.updated_by' => $this->updated_by,
            RepairRequest::tableName() . '.problem' => $this->problem,
            RepairRequest::tableName() . '.completed_by' => $this->completed_by,
            RepairRequest::tableName() . '.owner_id' => $this->owner_id,
            RepairRequest::tableName() . '.team_leader_id' => $this->team_leader_id,
            RepairRequest::tableName() . '.urgent_status' => $this->urgent_status,
            RepairRequest::tableName() . '.division_id' => $this->division_id,
            RepairRequest::tableName() . '.project_id' => $this->project_id,
            // RepairRequest::tableName() . '.location_id' => $this->location_id,
            RepairRequest::tableName() . '.category_id' => $this->category_id,
            // RepairRequest::tableName() . '.labor_charge' => $this->labor_charge,
        ]);


        $query->andFilterWhere(['like', RepairRequest::tableName() . '.requested_at', $this->requested_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.scheduled_at', $this->scheduled_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.informed_at', $this->informed_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.arrived_at', $this->arrived_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.departed_at', $this->departed_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.assigned_at', $this->assigned_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.customer_signature', $this->customer_signature])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.random_token', $this->random_token])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.completed_at', $this->completed_at])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.note', $this->note])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.labor_charge', $this->labor_charge])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.technician_signature', $this->technician_signature])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.reported_by_name', $this->reported_by_name])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.reported_by_phone', $this->reported_by_phone])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.notification_id', $this->notification_id])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.description', $this->description])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.repair_request_path', $this->repair_request_path])
            ->andFilterWhere(['like', RepairRequest::tableName() . '.service_note', $this->service_note])
            // LOCATION
            ->andFilterWhere(['like', Location::tableName() . '.id', $this->location_id])

            // ASSET
            ->andFilterWhere(['like', LocationEquipments::tableName() . '.meter_value', $this->meter_value])
            // ->andFilterWhere(['like', LocationEquipments::tableName() . '.code', $this->equipment_id])
            ->andFilterWhere(['like', new \yii\db\Expression('CONCAT(' . LocationEquipments::tableName() . '.code, " ", ' . Equipment::tableName() . '.name, " ", ' . Category::tableName() . '.name)'), $this->equipment_id])
            ->andFilterWhere(['like', EquipmentType::tableName() . '.meter_type', $this->meter_type])
            ->andFilterWhere(['=', new \yii\db\Expression('DATEDIFF(NOW(), repair_request.created_at) + 1'), $this->days_Overdue]);


        return $dataProvider;
    }
    public function formName()
    {
        return $this->formNameParam;
    }
}
