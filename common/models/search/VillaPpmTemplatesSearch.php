<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VillaPpmTemplates;

/**
 * VillaPpmTemplatesSearch represents the model behind the search form about `common\models\VillaPpmTemplates`.
 */
class VillaPpmTemplatesSearch extends VillaPpmTemplates
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sector_id', 'location_id', 'category_id', 'asset_id', 'project_id', 'frequency', 'repeating_condition', 'status', 'created_by', 'updated_by'], 'integer'],
            [['name', 'note', 'team_members', 'next_scheduled_date', 'starting_date_time', 'created_at', 'updated_at'], 'safe'],
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
        $query = VillaPpmTemplates::find();

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
            'sector_id' => $this->sector_id,
            'location_id' => $this->location_id,
            'category_id' => $this->category_id,
            'asset_id' => $this->asset_id,
            'project_id' => $this->project_id,
            'frequency' => $this->frequency,
            'repeating_condition' => $this->repeating_condition,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'team_members', $this->team_members])
            ->andFilterWhere(['like', 'next_scheduled_date', $this->next_scheduled_date])
            ->andFilterWhere(['like', 'starting_date_time', $this->starting_date_time])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
