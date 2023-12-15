<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WorkingHours;

/**
 * WorkingHoursSearch represents the model behind the search form about `common\models\WorkingHours`.
 */
class WorkingHoursSearch extends WorkingHours
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'year_month', 'status', 'created_by', 'updated_by'], 'integer'],
            [['daily_hours', 'holidays', 'created_at', 'updated_at'], 'safe'],
            [['total_hours'], 'number'],
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
        $query = WorkingHours::find();

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
            'year_month' => $this->year_month,
            'total_hours' => $this->total_hours,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'daily_hours', $this->daily_hours])
            ->andFilterWhere(['like', 'holidays', $this->holidays])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
