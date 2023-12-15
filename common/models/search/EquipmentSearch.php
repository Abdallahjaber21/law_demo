<?php

namespace common\models\search;

use common\models\Account;
use common\models\Category;
use common\models\Division;
use common\models\EquipmentType;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Equipment;

/**
 * EquipmentSearch represents the model behind the search form about `common\models\Equipment`.
 */
class EquipmentSearch extends Equipment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_type_id', 'status', 'created_by', 'updated_by', 'division_id', 'category_id'], 'integer'],
            [['id', 'code', 'name', 'created_at', 'updated_at', 'description'], 'safe'],
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
        $query = Equipment::find()->joinWith('division')->joinWith('category');
        if (empty($this->status)) {
            $this->status = Equipment::STATUS_ENABLED;
        }
        $query->joinWith('equipmentType');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['division_id'] = [
            'asc'  => [Division::tableName() . '.name' => SORT_ASC],
            'desc' => [Division::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['equipment_type_id'] = [
            'asc'  => [EquipmentType::tableName() . '.name' => SORT_ASC],
            'desc' => [EquipmentType::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['category_id'] = [
            'asc'  => [Category::tableName() . '.name' => SORT_ASC],
            'desc' => [Category::tableName() . '.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Equipment::tableName() . '.id' => $this->id,
            Equipment::tableName() . '.equipment_type_id' => $this->equipment_type_id,
            Equipment::tableName() . '.status' => $this->status,
            Equipment::tableName() . '.created_by' => $this->created_by,
            Equipment::tableName() . '.updated_by' => $this->updated_by,
            // 'division_id' => $this->division_id,
            Equipment::tableName() . '.category_id' => $this->category_id,
        ]);
        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([Equipment::tableName() . '.division_id' => $this->division_id]);
        } else {
            $query->andFilterWhere([Equipment::tableName() . '.division_id' => Yii::$app->user->identity->division_id]);
        }
        $query->andFilterWhere(['like',  Equipment::tableName() . '.code', $this->code])
            ->andFilterWhere(['like',  Equipment::tableName() . '.name', $this->name])
            ->andFilterWhere(['like',  Equipment::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Equipment::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', Equipment::tableName() . '.description', $this->description]);

        return $dataProvider;
    }
}
