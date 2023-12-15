<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EquipmentCaValue;

/**
 * EquipmentCaValueSearch represents the model behind the search form about `common\models\EquipmentCaValue`.
 */
class EquipmentCaValueSearch extends EquipmentCaValue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equipment_ca_id', 'equipment_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['value', 'created_at', 'updated_at'], 'safe'],
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
        $query = EquipmentCaValue::find();
        $query->innerJoin('equipment_ca', 'equipment_ca.id = equipment_ca_value.equipment_ca_id')
            ->leftJoin('equipment', 'equipment.id = equipment_ca_value.equipment_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'equipment_ca_id' => $this->equipment_ca_id,
            'equipment_id' => $this->equipment_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        // print_r($query->createCommand()->rawSql);
        // exit;
        return $dataProvider;
    }
}
