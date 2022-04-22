<?php

namespace modules\profiles\frontend\models;

use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use marketingsolutions\finance\models\Transaction;

/**
 * BonusesTransactionSearch represents the model behind the search form about `marketingsolutions\finance\models\Transaction`.
 */
class BonusesTransactionSearch extends Transaction
{
    /**
     * @var PrizeRecipientInterface
     */
    private $prizeRecipient;

    public function __construct(PrizeRecipientInterface $prizeRecipient, $config = [])
    {
        $this->prizeRecipient = $prizeRecipient;
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'purse_id', 'balance_before', 'amount', 'balance_after', 'partner_id'], 'integer'],
            [['type', 'partner_type', 'title', 'comment', 'created_at'], 'safe'],
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
        $query = Transaction::find();
        $query->where(['purse_id' => $this->prizeRecipient->recipientPurse->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'purse_id' => $this->purse_id,
            'balance_before' => $this->balance_before,
            'amount' => $this->amount,
            'balance_after' => $this->balance_after,
            'partner_id' => $this->partner_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'partner_type', $this->partner_type])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
