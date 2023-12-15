<?php

namespace common\models\search;

use common\models\Account;
use common\models\AccountType;
use common\models\Assignee;
use common\models\TechnicianSector;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Technician;
use common\models\Profession;
use common\models\Shift;
use common\models\Division;
use common\models\MainSector;
use common\models\TechnicianShift;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * TechnicianSearch represents the model behind the search form about `common\models\Technician`.
 */
class TechnicianSearch extends Technician
{
    public $type;
    public $superadmin_division_id;
    public $shift_id;

    public $work_status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'main_sector_id', 'login_attempts', 'status', 'created_by', 'updated_by', 'badge_number', 'profession_id'], 'integer'],
            [['name', 'code', 'title', 'email', 'country', 'password', 'phone_number', 'address', 'mobile_registration_id', 'web_registration_id', 'enable_notification', 'locked', 'last_login', 'timezone', 'language', 'created_at', 'updated_at', 'id'], 'safe'],
            [['type', 'image', 'superadmin_division_id', 'shift_id', 'work_status'], 'safe'],
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
        $query = '';

        $this->load($params);

        if (isset($this->work_status) && !empty($this->work_status)) {
            $subquery = (new \yii\db\Query())
                ->select(['user_id', 'status', 'MAX(updated_at) as max_updated_at'])
                ->from('assignee')
                // ->where(['status' => $this->work_status])
                ->groupBy(['user_id', 'status']);

            $subQ1 = Assignee::find()->select(['user_id', 'max(updated_at) as updated_at'])->groupBy('user_id');
            $subQ12 = Assignee::find()->alias('sub2')
                ->innerJoin(['sub1' => $subQ1], ['sub1.user_id' => new Expression('sub2.user_id'), 'sub1.updated_at' => new Expression('sub2.updated_at')])
                ->where(['sub2.status' => $this->work_status]);

            // print_r($subquery->createCommand()->rawSql);
            // exit;

            $query = Technician::find()
                ->innerJoin(['a' => $subQ12], 'a.user_id = technician.id');
        } else {
            $query = Technician::find()
                // ->join('LEFT JOIN', 'assignee', 'assignee.user_id = technician.id')
                ->orderBy(['updated_at' => SORT_DESC]);
        }



        if (empty($this->status)) {
            $this->status = Technician::STATUS_ENABLED;
        }

        // $query->joinWith(['technicianSectors'], false);
        $query->joinWith(['account', 'account.type0'], false);
        // $query->joinWith(['division'], false);
        // $query->joinWith(['mainSector'], false);
        // $query->joinWith(['profession'], false);
        // $query->innerJoin('account_type', 'account_type.id = account.type');
        $query->leftJoin("technician_shift", "technician.id = technician_shift.technician_id AND technician_shift.date = '" . date("Y-m-d") . "' ");

        // add conditions that should always apply here
        if (empty($this->status)) {
            $this->status = Technician::STATUS_ENABLED;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['division_id'] = [
            'asc' => [Division::tableName() . '.name' => SORT_ASC],
            'desc' => [Division::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['main_sector_id'] = [
            'asc' => [MainSector::tableName() . '.name' => SORT_ASC],
            'desc' => [MainSector::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['account_id'] = [
            'asc' => [AccountType::tableName() . '.name' => SORT_ASC],
            'desc' => [AccountType::tableName() . '.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['profession_id'] = [
            'asc' => [Profession::tableName() . '.name' => SORT_ASC],
            'desc' => [Profession::tableName() . '.name' => SORT_DESC],
        ];


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Technician::tableName() . '.id' => $this->id,
            Technician::tableName() . '.account_id' => $this->account_id,
            Technician::tableName() . '.login_attempts' => $this->login_attempts,
            Technician::tableName() . '.status' => $this->status,
            Technician::tableName() . '.created_by' => $this->created_by,
            Technician::tableName() . '.updated_by' => $this->updated_by,
            Technician::tableName() . '.main_sector_id' => $this->main_sector_id,
            Technician::tableName() . '.profession_id' => $this->profession_id,
        ]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            // $query->andFilterWhere([Technician::tableName() . '.sector_id' => $this->sector_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([Technician::tableName() . '.division_id' => Account::getAdminDivisionID()]);
        } else {
            $query->andFilterWhere([Technician::tableName() . '.main_sector_id' => Account::getAdminMainSectorId()]);
        }

        $query->andFilterWhere(['like', Technician::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', Technician::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', Technician::tableName() . '.image', $this->image])
            ->andFilterWhere(['like', Technician::tableName() . '.code', $this->code])
            ->andFilterWhere(['like', Technician::tableName() . '.badge_number', $this->badge_number])
            ->andFilterWhere(['like', Technician::tableName() . '.email', $this->email])
            ->andFilterWhere(['like', Technician::tableName() . '.country', $this->country])
            ->andFilterWhere(['like', Technician::tableName() . '.password', $this->password])
            ->andFilterWhere(['like', Technician::tableName() . '.phone_number', $this->phone_number])
            ->andFilterWhere(['like', Technician::tableName() . '.address', $this->address])
            ->andFilterWhere(['like', Technician::tableName() . '.mobile_registration_id', $this->mobile_registration_id])
            ->andFilterWhere(['like', Technician::tableName() . '.web_registration_id', $this->web_registration_id])
            ->andFilterWhere(['like', Technician::tableName() . '.enable_notification', $this->enable_notification])
            ->andFilterWhere(['like', Technician::tableName() . '.locked', $this->locked])
            ->andFilterWhere(['like', Technician::tableName() . '.last_login', $this->last_login])
            ->andFilterWhere(['like', Technician::tableName() . '.timezone', $this->timezone])
            ->andFilterWhere(['like', Technician::tableName() . '.language', $this->language])
            ->andFilterWhere(['like', Technician::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Technician::tableName() . '.updated_at', $this->updated_at]);

        if (isset($this->type) && !empty($this->type)) {
            $query->andFilterWhere(['=', Account::tableName() . '.type', $this->type]);
        }

        if (isset($this->superadmin_division_id) && !empty($this->superadmin_division_id)) {
            $query->andFilterWhere(['=', Technician::tableName() . '.division_id', $this->superadmin_division_id]);
        }

        if (isset($this->shift_id) && !empty($this->shift_id)) {
            $query->andFilterWhere([
                'AND',
                // [TechnicianShift::tableName() .  '.date' => date('Y-m-d')],
                [TechnicianShift::tableName() . '.shift_id' => $this->shift_id]
            ]);
        }

        if (isset($this->work_status) && !empty($this->work_status)) {

            $query->andFilterWhere(['a.status' => $this->work_status]);
        }




        return $dataProvider;
    }
}
