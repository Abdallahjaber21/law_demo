<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProfessionCategory;
use common\models\Category;

/**
 * ProfessionCategorySearch represents the model behind the search form about `common\models\ProfessionCategory`.
 */

class ProfessionCategorySearch extends ProfessionCategory
{
    public $cat_id;
    public $prof_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'prof_id', 'cat_id'], 'safe'],
            [['id', 'profession_id', 'category_id', 'status', 'created_by', 'updated_by'], 'integer'],
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
        $query = ProfessionCategory::find();
        if ($this->cat_id && $this->category_id) {
            $query->andFilterWhere(['category_id' => $this->category_id]);
        } else {
            $query->andFilterWhere(['category_id' => $this->cat_id]);
        }
        if ($this->prof_id && $this->profession_id) {
            $query->andFilterWhere(['profession_id' => $this->profession_id]);
        } else {
            $query->andFilterWhere(['profession_id' => $this->prof_id]);
        }




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
            'profession_id' => $this->profession_id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'cat_id' => $this->cat_id,
            'prof_id' => $this->prof_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
