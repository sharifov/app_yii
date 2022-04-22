<?php

namespace modules\sms\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use marketingsolutions\datetime\DateTimeBehavior;
use marketingsolutions\datetime\DateTimeRangeBehavior;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;
use modules\sms\common\models\SmsLog;


/**
 * SmsLogSearch represents the model behind the search form about `modules\sms\common\models\SmsLog`.
 */
class SmsLogSearch extends SmsLog implements SearchModelInterface
{


	public $created_at_start;
    public $created_at_end;


    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'datetime' => [
                'class' => DateTimeBehavior::className(),
                'originalFormat' => ['date', 'yyyy-MM-dd'],
                'targetFormat' => ['date', 'dd.MM.yyyy'],
                'attributes' => [
                    'created_at_start',
                    'created_at_end',
                ]
            ],
            'created_at_range' => [
                'class' => DateTimeRangeBehavior::className(),
                'startAttribute' => 'created_at_start_local',
                'endAttribute' => 'created_at_end_local',
                'targetAttribute' => 'created_at_range',
            ]
        ]);
    }


    public function init()
    {
        parent::init();

        if ($this->created_at_start === null) {
            $this->created_at_start = self::find()->min('DATE(created_at)');
        }
        if ($this->created_at_end === null) {
            $this->created_at_end = self::find()->max('DATE(created_at)');
        }
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sms_id', 'status'], 'integer'],
            [['type', 'service', 'phone_mobile', 'message', 'created_at', 'updated_at'], 'safe'],
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
        $query = $this->prepareQuery();
        $this->trigger(self::EVENT_AFTER_PREPARE_QUERY, new SearchModelEvent([
            'query' => $query,
        ]));

        $dataProvider = $this->prepareDataProvider($query);
        $this->trigger(self::EVENT_AFTER_PREPARE_DATA_PROVIDER, new SearchModelEvent([
            'query' => $query,
            'dataProvider' => $dataProvider,
        ]));

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->prepareFilters($query);
        $this->trigger(self::EVENT_AFTER_PREPARE_FILTERS, new SearchModelEvent([
            'query' => $query,
            'dataProvider' => $dataProvider,
        ]));

        return $dataProvider;
    }

    /**
     * @return ActiveQuery
     */
    protected function getQuery()
    {
        return SmsLog::find();
    }

    /**
     * @return ActiveQuery
     */
    protected function prepareQuery()
    {
        $query = $this->getQuery();
        return $query;
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'sms_id' => $this->sms_id,
            'status' => $this->status,
			'type' => $this->type
        ]);

        $query->andFilterWhere(['like', 'service', $this->service])
            ->andFilterWhere(['like', 'phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'message', $this->message]);

		$query->andFilterWhere(['between', 'DATE(created_at)', $this->created_at_start, $this->created_at_end]);

    }
}
