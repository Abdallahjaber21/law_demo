<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Admin;
use common\models\Account;
use common\models\AccountType;
use common\models\Division;
use common\models\MainSector;
use common\models\Users\AbstractAccount;
use yii\helpers\ArrayHelper;

/**
 * AdminSearch represents the model behind the search form about `common\models\Admin`.
 */
class AdminSearch extends Admin
{

    public $type;
    public $superadmin_division_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'status', 'login_attempts', 'division_id', 'main_sector_id'], 'integer'],
            [['id', 'superadmin_division_id', 'name', 'email', 'phone_number', 'country', 'password', 'address', 'image', 'auth_key', 'access_token', 'random_token', 'password_reset_token', 'mobile_registration_id', 'web_registration_id', 'enable_notification', 'locked', 'last_login', 'timezone', 'language', 'created_at', 'updated_at', 'badge_number', 'description', 'status', 'type'], 'safe'],
            // [['email', 'phone_number'], 'unique'],

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
        $query = Admin::find()->innerJoinWith('account')->joinWith('division')->joinWith('mainSector')->innerJoin('account_type', 'account_type.id = account.type');
        if (empty($this->status)) {

            $this->status = Admin::STATUS_ENABLED;
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
        $dataProvider->sort->attributes['account_id'] = [
            'asc'  => [AccountType::tableName() . '.name' => SORT_ASC],
            'desc' => [AccountType::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['main_sector_id'] = [
            'asc'  => [MainSector::tableName() . '.name' => SORT_ASC],
            'desc' => [MainSector::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'admin.id' => $this->id,
            'admin.status' => $this->status,
            'login_attempts' => $this->login_attempts,
            // 'division_id' =>  $this->division_id,
        ]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere(['admin.division_id' => $this->division_id]);
            $query->andFilterWhere(['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')]);
        } else {
            $query->andWhere(
                [
                    'AND',
                    ['admin.division_id' => Account::getAdminAccountTypeDivisionModel()->id],
                    ['IN', 'account_type.name', ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name')]
                ]
            );
            // $query->andFilterWhere(['admin.main_sector_id' => Account::getAdminMainSectorId()]);
        }

        $query->andFilterWhere(['like', 'admin.name', $this->name])
            ->andFilterWhere(['like', 'admin.email', $this->email])
            ->andFilterWhere(['like', 'admin.country', $this->country])
            ->andFilterWhere(['like', 'admin.password', $this->password])
            ->andFilterWhere(['like', 'admin.phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'admin.address', $this->address])
            ->andFilterWhere(['like', 'image', $this->image])
            // ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'admin.access_token', $this->access_token])
            ->andFilterWhere(['like', 'admin.random_token', $this->random_token])
            ->andFilterWhere(['like', 'admin.password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'admin.mobile_registration_id', $this->mobile_registration_id])
            ->andFilterWhere(['like', 'admin.web_registration_id', $this->web_registration_id])
            ->andFilterWhere(['like', 'admin.enable_notification', $this->enable_notification])
            ->andFilterWhere(['like', 'admin.locked', $this->locked])
            ->andFilterWhere(['like', 'admin.last_login', $this->last_login])
            ->andFilterWhere(['like', 'admin.timezone', $this->timezone])
            ->andFilterWhere(['like', 'admin.language', $this->language])
            ->andFilterWhere(['like', 'admin.created_at', $this->created_at])
            ->andFilterWhere(['like', 'admin.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'admin.badge_number', $this->badge_number])
            ->andFilterWhere(['like', 'admin.description', $this->description]);

        if (isset($this->type) && !empty($this->type)) {
            $query->andFilterWhere(['=', Account::tableName() . '.type', $this->type]);
        }

        if (isset($this->superadmin_division_id) && !empty($this->superadmin_division_id)) {
            $query->andFilterWhere(['=', Division::tableName() . '.id', $this->superadmin_division_id]);
        }

        if (isset($this->main_sector_id) && !empty($this->main_sector_id)) {
            $query->andFilterWhere(['=', 'admin.main_sector_id', $this->main_sector_id]);
        }


        // print_r($query->createCommand()->rawSql);
        // exit;
        return $dataProvider;
    }
}
