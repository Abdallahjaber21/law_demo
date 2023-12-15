<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EquipmentType;
use common\models\Category;

/**
 * EquipmentTypeSearch represents the model behind the search form about `common\models\EquipmentType`.
 */
class EquipmentTypeSearch extends EquipmentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'status', 'meter_type', 'alt_meter_type'], 'integer'],
            [['id', 'code', 'name', 'equivalance', 'reference_value'], 'safe'],
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
        $query = EquipmentType::find()->joinWith('category');

        if (empty($this->status)) {
            $this->status = EquipmentType::STATUS_ENABLED;
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['category_id'] = [
            'asc' => [Category::tableName() . '.name' => SORT_ASC],
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
            EquipmentType::tableName() . '.id' => $this->id,
            EquipmentType::tableName() . '.meter_type' => $this->meter_type,
            EquipmentType::tableName() . '.alt_meter_type' => $this->alt_meter_type,
            EquipmentType::tableName() . '.reference_value' => $this->reference_value,
            EquipmentType::tableName() . '.category_id' => $this->category_id,
            EquipmentType::tableName() . '.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', EquipmentType::tableName() . '.code', $this->code]);
        $query->andFilterWhere(['like', EquipmentType::tableName() . '.name', $this->name]);
        $query->andFilterWhere(['like', EquipmentType::tableName() . '.equivalance', $this->equivalance]);
        $query->andFilterWhere([EquipmentType::tableName() . '.category_id' => $this->category_id]);

        return $dataProvider;
    }
}
