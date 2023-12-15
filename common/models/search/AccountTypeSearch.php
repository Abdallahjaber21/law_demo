<?php

namespace common\models\search;

use common\models\Account;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountType;
use yii\helpers\ArrayHelper;
use common\models\Division;

/**
 * AccountTypeSearch represents the model behind the search form about `common\models\AccountType`.
 */
class AccountTypeSearch extends AccountType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['division_id', 'for_backend', 'parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'name', 'label', 'role_id', 'created_at', 'updated_at'], 'safe'],

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
        $query = AccountType::find()->joinWith('division');
        if (empty($this->status)) {
            $this->status = AccountType::STATUS_ENABLED;
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
            AccountType::tableName() . '.id' => $this->id,
            AccountType::tableName() . '.for_backend' => $this->for_backend,
            AccountType::tableName() . '.parent_id' => $this->parent_id,
            AccountType::tableName() . '.role_id' => $this->role_id,
            AccountType::tableName() . '.division_id' => $this->division_id,
            AccountType::tableName() . '.status' => $this->status,
            AccountType::tableName() . '.created_by' => $this->created_by,
            AccountType::tableName() . '.updated_by' => $this->updated_by,
        ]);

        $query->andWhere(
            [
                'OR',
                ['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')],
                ['=', 'for_backend', false]
            ]
        );

        $query->andFilterWhere(['like',   AccountType::tableName() . '.name', $this->name])
            ->andFilterWhere(['like',   AccountType::tableName() . '.label', $this->label])
            ->andFilterWhere(['like',   AccountType::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like',   AccountType::tableName() . '.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
