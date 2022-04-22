<?php

namespace modules\manual\backend\forms;

use ms\loyalty\bonuses\manual\common\finances\BackendUserPartner;
use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use ms\loyalty\contracts\profiles\ProfileInterface;
use marketingsolutions\finance\models\Transaction;
use yii\base\InvalidParamException;
use yii\base\Model;
use yz\admin\models\User;


/**
 * Class ChangeBalanceForm
 */
class ChangeBalanceForm extends Model
{
    /**
     * @var ProfileInterface | PrizeRecipientInterface
     */
    public $profile;
    /**
     * @var User
     */
    public $backendUser;

    /**
     * @var string
     */
    public $type;
    /**
     * @var integer
     */
    public $amount;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $comment;
    /**
     * @var
     */
    public $admin_id;
//    public $id;


    public function __construct(ProfileInterface $profile, User $backendUser, $config = [])
    {
        $this->profile = $profile;
        $this->backendUser = $backendUser;

        if (($this->profile instanceof PrizeRecipientInterface) == false) {
            throw new InvalidParamException('Profile class must be instanceof PrizeRecipientInterface');
        }

        parent::__construct($config);
    }

    public static function getTypeValues()
    {
        return [
            Transaction::INCOMING => 'Увеличить баланс счета',
            Transaction::OUTBOUND => 'Списать баллы со счета',
        ];
    }

    public function rules()
    {
        return [
            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::getTypeValues())],

            ['amount', 'required'],
            ['amount', 'integer', 'min' => 1],
            ['admin_id', 'integer'],
            ['amount', function () {
                if ($this->type == Transaction::INCOMING) {
                    return;
                }

                if ($this->profile->getRecipientPurse()->balance < $this->amount) {
                    $this->addError('amount', 'Сумма списания не может превышать текущий баланс счета участника');
                }
            }],

            ['title', 'required'],
            ['title', 'string'],

            ['comment', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => 'Операция',
            'amount' => 'Количество баллов',
            'title' => 'Название',
            'comment' => 'Комментарий',
        ];
    }

    public function change()
    {
        if ($this->validate() == false) {
            return false;
        }

        $transact = $this->profile->getRecipientPurse()->addTransaction(Transaction::factory(
            $this->type,
            $this->amount,
            new BackendUserPartner(['id' => $this->backendUser->id]),
            $this->title,
            $this->comment

        ));

        $addAdminId= Transaction::findOne(['purse_id'=>$this->profile->getRecipientPurse()->id, 'amount'=>$this->amount , 'admin_id'=>null ]);
        $addAdminId->admin_id = \Yii::$app->user->identity->id;
        $addAdminId->update(false);
        return $transact;
    }


}