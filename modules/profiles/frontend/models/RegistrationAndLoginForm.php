<?php

namespace modules\profiles\frontend\models;

use modules\profiles\common\models\Profile;
use ms\loyalty\contracts\identities\HasRoleInterface;
use ms\loyalty\contracts\identities\IdentityRegistrarInterface;
use ms\loyalty\contracts\identities\TokenProvidesEmailInterface;
use ms\loyalty\contracts\identities\TokenProvidesPhoneMobileInterface;
use ms\loyalty\contracts\identities\RegistrationTokenManagerInterface;
use yii\base\InvalidParamException;
use yii\base\Model;


/**
 * Class RegistrationAndLoginForm
 * @property Profile $profile
 */
class RegistrationAndLoginForm extends Model
{
    /**
     * @var string user password
     */
    public $password;
    /**
     * @var string user password repeat
     */
    public $passwordCompare;
    /**
     * @var bool
     */
    public $agreeWithTerms = true;
    /**
     * @var bool
     */
    public $allowPersonalDataProcessing = true;
    /**
     * @var \ms\loyalty\contracts\identities\RegistrationTokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var RegistrationProfile
     */
    private $profile;
    /**
     * @var IdentityRegistrarInterface
     */
    private $registrar;

    public function __construct(RegistrationTokenManagerInterface $tokenManager, IdentityRegistrarInterface $registrar, $config = [])
    {
        $this->tokenManager = $tokenManager;
        $this->registrar = $registrar;
        parent::__construct($config);
    }

    public function init()
    {
        if ($this->tokenManager instanceof TokenProvidesPhoneMobileInterface) {
            $this->profile = RegistrationProfile::findOne(['phone_mobile' => $this->tokenManager->getPhoneMobile()]);
        }

        if ($this->tokenManager instanceof TokenProvidesEmailInterface) {
            $this->profile = RegistrationProfile::findOne(['email' => $this->tokenManager->getEmail()]);
        }

        if ($this->profile === null) {
            throw new InvalidParamException('Profile with given properties was not found');
        }

        parent::init();
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['passwordCompare', 'required'],
            ['passwordCompare', 'compare', 'compareAttribute' => 'password'],
            ['agreeWithTerms', 'compare', 'compareValue' => 1,
                'message' => 'Вы должны согласиться с условиями участния в программе'],
            ['allowPersonalDataProcessing', 'compare', 'compareValue' => 1,
                'message' => 'Вы должны разрешить обработку своих персональных данных для участния в программе'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'passwordCompare' => 'Подтверждение пароля',
            'agreeWithTerms' => 'Согласен / согласна с правилами участия в программе',
            'allowPersonalDataProcessing' => 'Даю согласие на обработку своих персональных данных',
        ];
    }


    public function loadAll($data, $formName = null)
    {
        $result = true;
        $result = $this->load($data, $formName) && $result;
        $result = $this->profile->load($data) && $result;

        return $result;
    }

    public function process()
    {
        if ($this->validateAll() === false) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $identity = $this->registrar->createForProfile($this->profile, $this->password);
        $this->profile->identity_id = $identity->getId();
        $this->profile->save();

        \Yii::$app->user->login($identity);

        $transaction->commit();

        $this->tokenManager->remove();

        return true;
    }

    public function validateAll()
    {
        $result = true;
        $result = $this->validate() && $result;
        $result = $this->profile->validate() && $result;
        return $result;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

}