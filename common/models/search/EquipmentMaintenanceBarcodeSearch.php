<?php

namespace common\models\search;

use common\models\Equipment;
use common\models\EquipmentMaintenanceBarcode;
use common\models\Manufacturer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentMaintenanceBarcodeSearch represents the model behind the search form about `common\models\EquipmentMaintenanceBarcode`.
 */
class EquipmentMaintenanceBarcodeSearch extends EquipmentMaintenanceBarcode
{
    public $equipment_search;
    public $manufacturer_search;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equipment_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['location', 'barcode', 'created_at', 'updated_at', 'code'], 'safe'],
            [['equipment_search', 'manufacturer_search'], 'safe'],
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
        $query = EquipmentMaintenanceBarcode::find();
        $query->joinWith(['equipment']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['equipment_id' => SORT_ASC]]
        ]);
        $dataProvider->sort->attributes['equipment_search'] = [
            'asc'  => [Equipment::tableName() . '.code' => SORT_ASC],
            'desc' => [Equipment::tableName() . '.code' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['manufacturer_search'] = [
            'asc'  => [Manufacturer::tableName() . '.manufacturer' => SORT_ASC],
            'desc' => [Manufacturer::tableName() . '.manufacturer' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->equipment_search)) {
            $query->andFilterWhere(['like', Equipment::tableName() . '.code', $this->equipment_search]);
        }

        if (!empty($this->manufacturer_search)) {
            $query->andFilterWhere(['like', Equipment::tableName() . '.manufacturer', $this->manufacturer_search]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            EquipmentMaintenanceBarcode::tableName().'.id'           => $this->id,
            EquipmentMaintenanceBarcode::tableName().'.equipment_id' => $this->equipment_id,
            EquipmentMaintenanceBarcode::tableName().'.status'       => $this->status,
            EquipmentMaintenanceBarcode::tableName().'.created_by'   => $this->created_by,
            EquipmentMaintenanceBarcode::tableName().'.updated_by'   => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', EquipmentMaintenanceBarcode::tableName().'.location', $this->location])
            ->andFilterWhere(['like', EquipmentMaintenanceBarcode::tableName().'.barcode', $this->barcode])
            ->andFilterWhere(['like', EquipmentMaintenanceBarcode::tableName().'.created_at', $this->created_at])
            ->andFilterWhere(['like', EquipmentMaintenanceBarcode::tableName().'.updated_at', $this->updated_at])
            ->andFilterWhere(['like', EquipmentMaintenanceBarcode::tableName().'.code', $this->code]);

        return $dataProvider;
    }
}
