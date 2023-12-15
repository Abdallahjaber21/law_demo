<?php

namespace common\models\search;

use common\models\Division;
use common\models\Sector;
use common\models\MainSector;
use common\models\Technician;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SegmentPath;
use common\models\EquipmentPath;
use yii\helpers\ArrayHelper;

/**
 * SegmentPathSearch represents the model behind the search form about `common\models\SegmentPath`.
 */
class SegmentPathSearch extends SegmentPath
{

    public $division_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sector_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['id', 'name', 'value', 'created_at', 'updated_at', 'description', 'division_id', 'code'], 'safe'],
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
        $query = SegmentPath::find()->joinWith('sector.mainSector')->joinWith('sector.mainSector.division');
        if (empty($this->status)) {
            $this->status = SegmentPath::STATUS_ENABLED;
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
        $dataProvider->sort->attributes['sector_id'] = [
            'asc'  => [Sector::tableName() . '.name' => SORT_ASC],
            'desc' => [Sector::tableName() . '.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['equipment_path_id'] = [
            'asc'  => [EquipmentPath::tableName() . '.name' => SORT_ASC],
            'desc' => [EquipmentPath::tableName() . '.name' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'segment_path.id' => $this->id,
            'segment_path.sector_id' => $this->sector_id,
            'segment_path.status' => $this->status,
            'segment_path.created_by' => $this->created_by,
            'segment_path.updated_by' => $this->updated_by,
        ]);
        $technicianSectors = Technician::getTechnicianSectorsOptions();
        $sectorIds = ArrayHelper::getColumn($technicianSectors, 'id');
        $query->andFilterWhere(['IN', 'segment_path.sector_id', $sectorIds]);
        $query->andFilterWhere(['like', SegmentPath::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', SegmentPath::tableName() . '.code', $this->code])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', SegmentPath::tableName() . '.created_at', $this->created_at])
            ->andFilterWhere(['like', SegmentPath::tableName() . '.updated_at', $this->updated_at])
            ->andFilterWhere(['like', SegmentPath::tableName() . '.description', $this->description]);
        if (isset($this->division_id) && !empty($this->division_id)) {
            $query->andFilterWhere(['=', MainSector::tableName() . '.division_id', $this->division_id]);
        }

        return $dataProvider;
    }
}
