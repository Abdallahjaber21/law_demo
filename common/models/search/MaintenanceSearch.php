<?php

namespace common\models\search;

use common\models\Equipment;
use common\models\Location;
use common\models\Maintenance;
use common\models\Sector;
use common\models\Technician;
use common\models\users\Admin;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaintenanceSearch represents the model behind the search form about `common\models\Maintenance`.
 */
class MaintenanceSearch extends Maintenance
{
    public $sector_id;
    public $location_search;
    public $equipment_search;
    public $equipment_type;
    public $manufacturer;
    public $material;
    public $contract_material;
    public $contract_expire_at;
    public $temporary_in;
    public $temporary_out;
    public $visit_number;
    public $hidden_status;
    public $duration_min;
    public $duration_max;
    public $atl_name;
    ////
    ///
    public $completed_at_from;
    public $completed_at_to;
    public $first_scan_at_from;
    public $first_scan_at_to;
    public $created_at_from;
    public $created_at_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equipment_id', 'location_id', 'technician_id', 'year', 'month', 'created_by', 'updated_by'], 'integer'],
            [['random_token', 'note', 'customer_name', 'customer_signature', 'technician_signature', 'created_at', 'updated_at'], 'safe'],
            [['status', 'hidden_status'], 'safe'],
            [['remaining_barcodes', 'atl_status'], 'integer'],
            [['first_scan_at', 'completed_at'], 'safe'],
            [['manufacturer', 'location_search', 'equipment_search', 'equipment_type', 'contract_material', 'contract_expire_at'], 'safe'],
            [['material'], 'safe'],
            [['temporary_in', 'temporary_out', 'visit_number'], 'safe'],
            [['complete_method', 'completed_by_atl', 'duration'], 'integer'],
            [['duration_min', 'duration_max'], 'integer'],
            [['atl_name'], 'safe'],
            ////
            [['completed_at_from', 'completed_at_to'], 'safe'],
            [['first_scan_at_from', 'first_scan_at_to'], 'safe'],
            [['created_at_from', 'created_at_to'], 'safe'],
            [['sector_id'], 'safe'],
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
        $query = Maintenance::find();
        $query->joinWith(['equipment', 'location', 'location.sector', 'technician', 'completedByAtl']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => [
                'completed_at'  => SORT_DESC,
                'first_scan_at' => SORT_DESC,
                'id'            => SORT_DESC,
            ]
            ],
            'pagination' => [
                'pageSize' => 50
            ]
        ]);

        $dataProvider->sort->attributes['sector_id'] = [
            'asc'  => [Sector::tableName() . '.code' => SORT_ASC],
            'desc' => [Sector::tableName() . '.code' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['technician_id'] = [
            'asc'  => [Technician::tableName() . '.name' => SORT_ASC],
            'desc' => [Technician::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['location_search'] = [
            'asc'  => [Location::tableName() . '.code' => SORT_ASC],
            'desc' => [Location::tableName() . '.code' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['equipment_search'] = [
            'asc'  => [Equipment::tableName() . '.code' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.code' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['equipment_type'] = [
            'asc'  => [Equipment::tableName() . '.equipment_type' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.equipment_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['manufacturer'] = [
            'asc'  => [Equipment::tableName() . '.manufacturer' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.manufacturer' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['material'] = [
            'asc'  => [Equipment::tableName() . '.material' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.material' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['contract_material'] = [
            'asc'  => [Equipment::tableName() . '.material' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.material' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['contract_expire_at'] = [
            'asc'  => [Equipment::tableName() . '.expire_at' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.expire_at' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['temporary_in'] = [
            'asc'  => [Equipment::tableName() . '.temporary_in' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.temporary_in' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['temporary_out'] = [
            'asc'  => [Equipment::tableName() . '.temporary_out' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.temporary_out' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['atl_name'] = [
            'asc'  => [Admin::tableName() . '.name' => SORT_ASC],
            'desc' => [Admin::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }


        if (!empty($this->material)) {
            if (in_array("ALL_IN", $this->material)) {
                $this->material = Equipment::getActiveContracts();
            } else if (in_array("ALL_OUT", $this->material)) {
                $this->material = Equipment::getInActiveContracts();
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Maintenance::tableName() . '.id'                 => $this->id,
            Maintenance::tableName() . '.equipment_id'       => $this->equipment_id,
            Maintenance::tableName() . '.location_id'        => $this->location_id,
            Maintenance::tableName() . '.technician_id'      => $this->technician_id,
            Maintenance::tableName() . '.status'             => $this->status,
            Maintenance::tableName() . '.atl_status'         => $this->atl_status,
            Maintenance::tableName() . '.year'               => $this->year,
            Maintenance::tableName() . '.month'              => $this->month,
            Maintenance::tableName() . '.created_by'         => $this->created_by,
            Maintenance::tableName() . '.updated_by'         => $this->updated_by,
            Location::tableName() . '.sector_id'             => $this->sector_id,
            Maintenance::tableName() . '.visit_number'       => $this->visit_number,
            Maintenance::tableName() . '.remaining_barcodes' => $this->remaining_barcodes,
            Maintenance::tableName() . '.complete_method'    => $this->complete_method,
            Maintenance::tableName() . '.completed_by_atl'   => $this->completed_by_atl,
            Equipment::tableName() . '.material'   => $this->material,
        ]);

        if (!empty($this->duration_min)) {
            $query->andFilterWhere(['>=', 'duration', $this->duration_min]);
        }
        if (!empty($this->duration_max)) {
            $query->andFilterWhere(['<=', 'duration', $this->duration_max]);
        }

        if (!empty($this->location_search)) {
            $query->andFilterWhere([
                'OR',
                ['like', Location::tableName() . '.name', $this->location_search],
                ['like', Location::tableName() . '.code', $this->location_search],
            ]);
        }
        if (!empty($this->atl_name)) {
            $query->andFilterWhere(['like', Admin::tableName() . '.name', $this->atl_name]);
        }
        if (!empty($this->equipment_search)) {
            $query->andFilterWhere([
                'OR',
                ['like', Equipment::tableName() . '.name', $this->equipment_search],
                ['like', Equipment::tableName() . '.code', $this->equipment_search],
            ]);
        }

        if (!empty($this->completed_at_from)) {
            $query->andFilterWhere(['>=', Maintenance::tableName() . '.completed_at', $this->completed_at_from . ' 00:00:00']);
        }
        if (!empty($this->completed_at_to)) {
            $query->andFilterWhere(['<=', Maintenance::tableName() . '.completed_at', $this->completed_at_to . ' 23:59:59']);
        }
        if (!empty($this->first_scan_at_from)) {
            $query->andFilterWhere(['>=', Maintenance::tableName() . '.first_scan_at', $this->first_scan_at_from . ' 00:00:00']);
        }
        if (!empty($this->first_scan_at_to)) {
            $query->andFilterWhere(['<=', Maintenance::tableName() . '.first_scan_at', $this->first_scan_at_to . ' 23:59:59']);
        }
        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', Maintenance::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', Maintenance::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }

        $query->andFilterWhere([Maintenance::tableName() . '.status' => $this->hidden_status]);
        $query->andFilterWhere(['like', Equipment::tableName() . '.equipment_type', $this->equipment_type]);
        $query->andFilterWhere(['like', Equipment::tableName() . '.manufacturer', $this->manufacturer]);
        //$query->andFilterWhere(['like', Equipment::tableName() . '.material', $this->material]);
        $query->andFilterWhere(['like', Equipment::tableName() . '.material', $this->contract_material]);

        if (!empty($this->contract_expire_at)) {
            $query->andFilterWhere(['<', Equipment::tableName() . '.expire_at', $this->contract_expire_at . ' 00:00:00']);
        }

        $query->andFilterWhere(['like', Equipment::tableName() . '.temporary_in', $this->temporary_in]);
        $query->andFilterWhere(['like', Equipment::tableName() . '.temporary_out', $this->temporary_out]);

        $query
//            ->andFilterWhere(['like', Maintenance::tableName().'.random_token', $this->random_token])
            ->andFilterWhere(['like', Maintenance::tableName() . '.note', $this->note])
            ->andFilterWhere(['like', Maintenance::tableName() . '.customer_name', $this->customer_name])
            ->andFilterWhere(['like', Maintenance::tableName() . '.customer_signature', $this->customer_signature])
            ->andFilterWhere(['like', Maintenance::tableName() . '.technician_signature', $this->technician_signature])
            ->andFilterWhere(['like', Maintenance::tableName() . '.first_scan_at', $this->first_scan_at])
            ->andFilterWhere(['like', Maintenance::tableName() . '.completed_at', $this->completed_at])
            ->andFilterWhere(['like', Maintenance::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Maintenance::tableName() . '.updated_at', $this->updated_at]);
//print_r($query->createCommand()->rawSql);exit();
        return $dataProvider;
    }
}
