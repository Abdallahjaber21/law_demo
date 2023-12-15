<?php

namespace common\models\users\search;

use common\models\AdminSector;
use common\models\users\Admin;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AdminSearch represents the model behind the search form about `common\models\users\Admin`.
 */
class AdminSearch extends Admin
{

    public $sector_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'status', 'enable_notification', 'locked', 'login_attempts'], 'integer'],
            [['name', 'email', 'phone_number', 'address', 'random_token', 'password_reset_token', 'mobile_registration_id', 'web_registration_id', 'last_login', 'created_at', 'updated_at'], 'safe'],
            [['sector_id'], 'safe']
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
        $query = Admin::find();
        $query->joinWith(['adminSectors'], false);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            Admin::tableName() . '.id'                  => $this->id,
            Admin::tableName() . '.account_id'          => $this->account_id,
            Admin::tableName() . '.status'              => $this->status,
            Admin::tableName() . '.enable_notification' => $this->enable_notification,
            Admin::tableName() . '.locked'              => $this->locked,
            Admin::tableName() . '.login_attempts'      => $this->login_attempts,
            AdminSector::tableName() . '.sector_id'      => $this->sector_id,
        ]);

        $query->andFilterWhere(['like', Admin::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', Admin::tableName() . '.email', $this->email])
            ->andFilterWhere(['like', Admin::tableName() . '.phone_number', $this->phone_number])
            ->andFilterWhere(['like', Admin::tableName() . '.address', $this->address])
            ->andFilterWhere(['like', Admin::tableName() . '.random_token', $this->random_token])
            ->andFilterWhere(['like', Admin::tableName() . '.password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', Admin::tableName() . '.mobile_registration_id', $this->mobile_registration_id])
            ->andFilterWhere(['like', Admin::tableName() . '.last_login', $this->last_login])
            ->andFilterWhere(['like', Admin::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Admin::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', Admin::tableName() . '.web_registration_id', $this->web_registration_id]);


        return $dataProvider;
    }
}
