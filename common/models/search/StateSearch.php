<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\State;
use common\models\Country;

/**
 * StateSearch represents the model behind the search form about `common\models\State`.
 */
class StateSearch extends State
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
            [['country_id', 'status', 'created_by', 'updated_by'], 'integer'],
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
        $query = State::find()->joinWith('country');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['country_id'] = [
            'asc'  => [Country::tableName() . '.name' => SORT_ASC],
            'desc' => [Country::tableName() . '.name' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            State::tableName() . '.id' => $this->id,
            State::tableName() . '.country_id' => $this->country_id,
            State::tableName() . '.status' => $this->status,
            State::tableName() . '.created_by' => $this->created_by,
            State::tableName() . '.updated_by' => $this->updated_by,
        ]);
        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', State::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', State::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        if (!empty($this->updated_at_from)) {
            $query->andFilterWhere(['>=', State::tableName() . '.updated_at', $this->updated_at_from . ' 00:00:00']);
        }
        if (!empty($this->updated_at_to)) {
            $query->andFilterWhere(['<=', State::tableName() . '.updated_at', $this->updated_at_to . ' 23:59:59']);
        }
        $query->andFilterWhere(['like',  State::tableName() . '.name', $this->name])
            ->andFilterWhere(['like',  State::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like',  State::tableName() . '.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
