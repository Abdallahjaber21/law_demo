<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EngineOilTypes;

/**
 * EngineOilTypesSearch represents the model behind the search form about `common\models\EngineOilTypes`.
 */
class EngineOilTypesSearch extends EngineOilTypes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['motor_fuel_type_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'oil_viscosity', 'created_at', 'updated_at'], 'safe'],
            [['can_weight', 'oil_durability'], 'safe'],
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
        $query = EngineOilTypes::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'motor_fuel_type_id' => $this->motor_fuel_type_id,
            'can_weight' => $this->can_weight,
            'oil_durability' => $this->oil_durability,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'oil_viscosity', $this->oil_viscosity])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
