<?php

namespace common\models\search;

use common\models\Account;
use common\models\Division;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\MainSector;
use common\models\Sector;
use yii\helpers\ArrayHelper;

/**
 * LocationSearch represents the model behind the search form about `common\models\Location`.
 */
class LocationSearch extends Location
{

    public $main_sector_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sector_id', 'status', 'created_by', 'updated_by', 'division_id'], 'integer'],
            [['id',  'owner_phone', 'code', 'name', 'created_at', 'updated_at', 'address', 'latitude', 'longitude', 'is_restricted', 'owner', 'main_sector_id'], 'safe'],
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
        $query = Location::find()->joinWith('sector')->joinWith('division');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['division_id'] = [
            'asc'  => [Division::tableName() . '.name' => SORT_ASC],
            'desc' => [Division::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['sector_id'] = [
            'asc'  => [Sector::tableName() . '.name' => SORT_ASC],
            'desc' => [Sector::tableName() . '.name' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (empty($this->status)) {
            $this->status = Location::STATUS_ENABLED;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Location::tableName() . '.id' => $this->id,
            Location::tableName() . '.sector_id' => $this->sector_id,
            Location::tableName() . '.status' => $this->status,
            Location::tableName() . '.created_by' => $this->created_by,
            Location::tableName() . '.updated_by' => $this->updated_by,
            // 'division_id' => $this->division_id,
        ]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $query->andFilterWhere([Location::tableName() . '.division_id' => $this->division_id]);
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $query->andFilterWhere([Location::tableName() . '.division_id' =>  Yii::$app->user->identity->division_id]);
        } else {
            $query->andWhere([Location::tableName() . '.division_id' => Yii::$app->user->identity->division_id, Location::tableName() . '.sector_id' => ArrayHelper::getColumn(MainSector::findOne(@Account::getAdminMainSectorId())->sectors, 'id')]);
        }

        if (!empty($this->main_sector_id)) {
            $query->andFilterWhere([Sector::tableName() . '.main_sector_id' => $this->main_sector_id]);
        }

        $query->andFilterWhere(['like', Location::tableName() . '.code', $this->code])
            ->andFilterWhere(['like', Location::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', Location::tableName() . '.owner_phone', $this->owner_phone])
            ->andFilterWhere(['like', Location::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', Location::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', Location::tableName() . '.address', $this->address])
            ->andFilterWhere(['like', Location::tableName() . '.latitude', $this->latitude])
            ->andFilterWhere(['like', Location::tableName() . '.longitude', $this->longitude])
            // ->andFilterWhere(['like', 'is_restricted', $this->is_restricted])
            ->andFilterWhere(['like', Location::tableName() . '.owner', $this->owner]);

        return $dataProvider;
    }
}
