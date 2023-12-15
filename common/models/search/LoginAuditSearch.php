<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoginAudit;

/**
 * LoginAuditSearch represents the model behind the search form about `common\models\LoginAudit`.
 */
class LoginAuditSearch extends LoginAudit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['id'], 'integer'],
            [['id', 'ip_address', 'login_credential', 'login_status', 'datetime', 'logout'], 'safe'],
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
        $query = LoginAudit::find();

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
        ]);

        $query->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'login_credential', $this->login_credential])
            ->andFilterWhere(['like', 'login_status', $this->login_status])
            ->andFilterWhere(['like', 'datetime', $this->datetime])
            ->andFilterWhere(['like', 'logout', $this->logout]);

        return $dataProvider;
    }
}
