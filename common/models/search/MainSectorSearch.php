<?php

namespace common\models\search;

use common\models\Account;
use common\models\Division;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MainSector;

/**
 * MainSectorSearch represents the model behind the search form about `common\models\MainSector`.
 */
class MainSectorSearch extends MainSector
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
            [['status', 'created_by', 'updated_by', 'division_id'], 'integer'],
            [['id', 'name', 'description', 'created_at', 'updated_at', 'created_at_from', 'created_at_to', 'updated_at_from', 'updated_at_to'], 'safe'],
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
        $query = MainSector::find()->joinWith('division');
        // add conditions to retrieve only the active main sector when the page loads, otherwise, when filtering, other statuses become visible
        if (empty($this->status)) {
            $this->status = MainSector::STATUS_ENABLED;
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['division_id'] = [
            'asc'  => [Division::tableName() . '.name' => SORT_ASC],
            'desc' => [Division::tableName() . '.name' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            MainSector::tableName() . '.id' => $this->id,
            MainSector::tableName() .  '.status' => $this->status,
            MainSector::tableName() .  '.created_by' => $this->created_by,
            MainSector::tableName() . '.updated_by' => $this->updated_by,
            // 'division_id' => $this->division_id,
        ]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' => $this->division_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' =>  Yii::$app->user->identity->division_id]);
        } else {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' => Yii::$app->user->identity->division_id]);
            $query->andFilterWhere([MainSector::tableName() . '.id' => Account::getAdminMainSectorId()]);
        }

        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', MainSector::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', MainSector::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        if (!empty($this->updated_at_from)) {
            $query->andFilterWhere(['>=', MainSector::tableName() . '.updated_at', $this->updated_at_from . ' 00:00:00']);
        }
        if (!empty($this->updated_at_to)) {
            $query->andFilterWhere(['<=', MainSector::tableName() . '.updated_at', $this->updated_at_to . ' 23:59:59']);
        }

        $query->andFilterWhere(['like', MainSector::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', MainSector::tableName() . '.description', $this->description])
            ->andFilterWhere(['like', MainSector::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', MainSector::tableName() . '.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
