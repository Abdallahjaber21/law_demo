<?php

namespace common\models\search;

use common\models\Account;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Assignee;
use common\models\RepairRequest;
use common\models\Technician;

/**
 * AssigneeSearch represents the model behind the search form about `common\models\Assignee`.
 */
class AssigneeSearch extends Assignee
{
    public $technician_name, $profession_id, $main_sector_id, $badge_number, $account_id, $work_order_type, $work_status;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'repair_request_id', 'user_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['description', 'datetime', 'created_at', 'updated_at', 'main_sector_id', 'profession_id', 'work_status', 'account_id', 'work_order_type', 'badge_number', 'technician_name'], 'safe'],
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
        $query = Assignee::find()->joinWith(['user u', 'user.account a']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'sort' => ['defaultOrder' => [Assignee::tableName() . '.id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Assignee::tableName() . '.id' => $this->id,
            'repair_request_id' => $this->repair_request_id,
            'user_id' => $this->user_id,
            Assignee::tableName() . '.status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'datetime', $this->datetime])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['=', Technician::tableName() . '.main_sector_id', $this->main_sector_id])
            ->andFilterWhere(['=', Technician::tableName() . '.profession_id', $this->profession_id])
            ->andFilterWhere(['like', Technician::tableName() . '.badge_number', $this->badge_number])
            ->andFilterWhere(['like', Technician::tableName() . '.name', $this->technician_name])
            ->andFilterWhere(['like',  RepairRequest::tableName() . '.service_type', $this->work_order_type])
            ->andFilterWhere(['like', 'a.type', $this->account_id]);

        if (isset($this->work_status) && !empty($this->work_status)) {
            $query->andFilterWhere(['assignee.status' => $this->work_status]);
        }





        return $dataProvider;
    }
}
