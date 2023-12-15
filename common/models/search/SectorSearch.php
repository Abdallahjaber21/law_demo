<?php

namespace common\models\search;

use common\models\Account;
use common\models\Division;
use common\models\MainSector;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Sector;

/**
 * SectorSearch represents the model behind the search form about `common\models\Sector`.
 */
class SectorSearch extends Sector
{
    public $created_at_from;
    public $created_at_to;
    public $updated_at_from;
    public $updated_at_to;

    public $division_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'country_id', 'state_id', 'city_id', 'created_by', 'updated_by', 'default_technician_id', 'main_sector_id'], 'integer'],
            [['id', 'code', 'name', 'created_at', 'updated_at', 'description', 'created_at_from', 'created_at_to', 'updated_at_from', 'updated_at_to', 'division_id'], 'safe'],
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
        $query = Sector::find()->joinWith('mainSector', 'mainSector.division');
        if (empty($this->status)) {
            $this->status = Sector::STATUS_ENABLED;
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['main_sector_id'] = [
            'asc'  => [MainSector::tableName() . '.name' => SORT_ASC],
            'desc' => [MainSector::tableName() . '.name' => SORT_DESC],
        ];
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
            Sector::tableName() . '.id' => $this->id,
            Sector::tableName() . '.country_id' => $this->country_id,
            Sector::tableName() . '.state_id' => $this->state_id,
            Sector::tableName() . '.city_id' => $this->city_id,
            Sector::tableName() . '.status' => $this->status,
            Sector::tableName() . '.created_by' => $this->created_by,
            Sector::tableName() . '.updated_by' => $this->updated_by,
            Sector::tableName() . '.default_technician_id' => $this->default_technician_id,
            // 'main_sector_id' => $this->main_sector_id,
        ]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => $this->main_sector_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([MainSector::tableName() . '.division_id' =>  Yii::$app->user->identity->division_id]);
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => $this->main_sector_id]);
        } else {
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => Yii::$app->user->identity->main_sector_id]);
        }

        if (!empty($this->created_at_from)) {
            $query->andFilterWhere(['>=', Sector::tableName() . '.created_at', $this->created_at_from . ' 00:00:00']);
        }
        if (!empty($this->created_at_to)) {
            $query->andFilterWhere(['<=', Sector::tableName() . '.created_at', $this->created_at_to . ' 23:59:59']);
        }
        if (!empty($this->updated_at_from)) {
            $query->andFilterWhere(['>=', Sector::tableName() . '.updated_at', $this->updated_at_from . ' 00:00:00']);
        }
        if (!empty($this->updated_at_to)) {
            $query->andFilterWhere(['<=', Sector::tableName() . '.updated_at', $this->updated_at_to . ' 23:59:59']);
        }
        if (!empty($this->division_id)) {
            $query->andFilterWhere(['=', MainSector::tableName() . '.division_id', $this->division_id]);
        }
        $query->andFilterWhere(['=', Sector::tableName() . '.code', $this->code])
            ->andFilterWhere(['like', Sector::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', Sector::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Sector::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', Sector::tableName() . '.description', $this->description]);

        return $dataProvider;
    }
}
