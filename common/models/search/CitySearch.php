<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\City;
use common\models\State;

/**
 * CitySearch represents the model behind the search form about `common\models\City`.
 */
class CitySearch extends City
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
            [['state_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'name', 'created_at', 'updated_at', 'created_at_from', 'created_at_to', 'updated_at_to', 'updated_at_from'], 'safe'],

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
        $query = City::find()->joinWith('state');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['state_id'] = [
            'asc'  => [State::tableName() . '.name' => SORT_ASC],
            'desc' => [State::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            City::tableName() . '.id' => $this->id,
            City::tableName() . '.state_id' => $this->state_id,
            City::tableName() . '.status' => $this->status,
            City::tableName() . '.created_by' => $this->created_by,
            City::tableName() . '.updated_by' => $this->updated_by,
        ]);
        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', City::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', City::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        if (!empty($this->updated_at_from)) {
            $query->andFilterWhere(['>=', City::tableName() . '.updated_at', $this->updated_at_from . ' 00:00:00']);
        }
        if (!empty($this->updated_at_to)) {
            $query->andFilterWhere(['<=', City::tableName() . '.updated_at', $this->updated_at_to . ' 23:59:59']);
        }
        $query->andFilterWhere(['like',  City::tableName() . '.name', $this->name])
            ->andFilterWhere(['like',  City::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like',  City::tableName() . '.updated_at', $this->updated_at]);


        return $dataProvider;
    }
}
