<?php

namespace common\models\search;

use common\models\CoordinatesIssue;
use common\models\Location;
use common\models\Technician;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CoordinatesIssueSearch represents the model behind the search form about `common\models\CoordinatesIssue`.
 */
class CoordinatesIssueSearch extends CoordinatesIssue
{
    public $reported_by_name;
    public $location_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'reported_by', 'status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'old_latitude', 'old_longitude', 'new_latitude', 'new_longitude', 'created_at', 'updated_at'], 'safe'],
            [['reported_by_name', 'location_name'], 'safe']
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
        $query = CoordinatesIssue::find();
        $query->joinWith(['reportedBy', 'location'], false);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['reported_by_name'] = [
            'asc' => [Technician::tableName() . '.name' => SORT_ASC],
            'desc' => [Technician::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['location_name'] = [
            'asc' => [Location::tableName() . '.code' => SORT_ASC],
            'desc' => [Location::tableName() . '.code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            CoordinatesIssue::tableName() . '.id' => $this->id,
            CoordinatesIssue::tableName() . '.location_id' => $this->location_id,
            CoordinatesIssue::tableName() . '.reported_by' => $this->reported_by,
            CoordinatesIssue::tableName() . '.status' => $this->status,
            CoordinatesIssue::tableName() . '.created_by' => $this->created_by,
            CoordinatesIssue::tableName() . '.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', CoordinatesIssue::tableName() . '.old_latitude', $this->old_latitude])
            ->andFilterWhere(['like', CoordinatesIssue::tableName() . '.old_longitude', $this->old_longitude])
            ->andFilterWhere(['like', CoordinatesIssue::tableName() . '.new_latitude', $this->new_latitude])
            ->andFilterWhere(['like', CoordinatesIssue::tableName() . '.new_longitude', $this->new_longitude])
            ->andFilterWhere(['like', Technician::tableName() . '.name', $this->reported_by_name])
            ->andFilterWhere(['like', CoordinatesIssue::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', CoordinatesIssue::tableName() . '.updated_at', $this->updated_at]);

        $query->andFilterWhere([
            'OR',
            [Location::tableName() . '.code' => $this->location_name],
            ['like', Location::tableName() . '.name', $this->location_name],
        ]);

        return $dataProvider;
    }
}
