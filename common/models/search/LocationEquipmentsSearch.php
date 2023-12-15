<?php

namespace common\models\search;

use common\models\Equipment;
use common\models\Location;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LocationEquipments;

/**
 * LocationEquipmentsSearch represents the model behind the search form about `common\models\LocationEquipments`.
 */
class LocationEquipmentsSearch extends LocationEquipments
{

    public $equipment_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'division_id', 'driver_id', 'equipment_id', 'status', 'created_by', 'updated_by', 'meter_damaged', 'safety_status'], 'integer'],
            [['meter_value', 'id', 'code', 'value', 'remarks', 'created_at', 'updated_at', 'equipment_name', 'motor_fuel_type', 'chassie_number'], 'safe'],
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
    public function search($params, $order = null)
    {
        $query = LocationEquipments::find()->joinWith('equipment');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => empty($order) ? SORT_DESC : SORT_ASC]]
        ]);

        $this->load($params);

        $this->location_id = Yii::$app->request->get('location_id');
        $this->meter_damaged = Yii::$app->request->get('meter_damaged');

        // print_r($this->location_id);
        // exit;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            LocationEquipments::tableName() . '.id' => $this->id,
            LocationEquipments::tableName() . '.meter_value' => $this->meter_value,
            LocationEquipments::tableName() . '.meter_damaged' => $this->meter_damaged,
            LocationEquipments::tableName() . '.location_id' => $this->location_id,
            LocationEquipments::tableName() . '.division_id' => $this->division_id,
            LocationEquipments::tableName() . '.driver_id' => $this->driver_id,
            LocationEquipments::tableName() . '.equipment_id' => $this->equipment_id,
            LocationEquipments::tableName() . '.status' => $this->status,
            LocationEquipments::tableName() . '.remarks' => $this->remarks,
            LocationEquipments::tableName() . '.created_by' => $this->created_by,
            LocationEquipments::tableName() . '.updated_by' => $this->updated_by,
            LocationEquipments::tableName() . '.motor_fuel_type' => $this->motor_fuel_type,
            LocationEquipments::tableName() . '.safety_status' => $this->safety_status,
        ]);

        $query->andFilterWhere(['like', LocationEquipments::tableName() . '.code', $this->code])
            ->andFilterWhere(['like', LocationEquipments::tableName() . '.value', $this->value])
            ->andFilterWhere(['like', LocationEquipments::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', LocationEquipments::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', LocationEquipments::tableName() . '.chassie_number', $this->chassie_number])
            ->andFilterWhere(['like', Equipment::tableName() . '.name', $this->equipment_name]);
        return $dataProvider;
    }
}
