<?php

namespace common\models\search;

use common\models\Category;
use common\models\Profession;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProfessionCategory as ProfessionCategoryModel;

/**
 * ProfessionCategory represents the model behind the search form about `common\models\ProfessionCategory`.
 */
class ProfessionCategory extends ProfessionCategoryModel
{
    public $cat_id;
    public $prof_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'cat_id', 'prof_id'], 'safe'],
            [['profession_id', 'category_id', 'status', 'created_by', 'updated_by'], 'integer'],
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

        $query = ProfessionCategoryModel::find()->joinWith('category')->joinWith('profession');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['cat_id'] = [
            'asc'  => [Category::tableName() . '.name' => SORT_ASC],
            'desc' => [Category::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['profession_id'] = [
            'asc'  => [Profession::tableName() . '.name' => SORT_ASC],
            'desc' => [Profession::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }
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
        // grid filtering conditions
        $query->andFilterWhere([
            ProfessionCategory::tableName() . '.id' => $this->id,
            'profession_id' => $this->profession_id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,

        ]);


        $query->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
