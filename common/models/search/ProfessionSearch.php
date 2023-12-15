<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Profession;

/**
 * ProfessionSearch represents the model behind the search form about `common\models\Profession`.
 */
class ProfessionSearch extends Profession
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
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'name', 'description', 'created_at', 'created_at_from', 'created_at_to', 'updated_at', 'updated_at_from', 'updated_at_to'], 'safe'],

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
        $query = Profession::find();
        if (empty($this->status)) {
            $this->status = Profession::STATUS_ENABLED;
        }

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
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', Profession::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', Profession::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        if (!empty($this->updated_at_from)) {
            $query->andFilterWhere(['>=', Profession::tableName() . '.updated_at', $this->updated_at_from . ' 00:00:00']);
        }
        if (!empty($this->updated_at_to)) {
            $query->andFilterWhere(['<=', Profession::tableName() . '.updated_at', $this->updated_at_to . ' 23:59:59']);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
