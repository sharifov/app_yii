<?php

namespace modules\profiles\common\mailing;
use modules\profiles\common\models\Profile;
use yz\admin\mailer\common\mailing\MailRecipientInterface;
use yz\admin\mailer\common\models\Mail;
use yz\admin\mailer\common\mailing\MailRecipient;


/**
 * Class ProfileRecipient
 */
class ProfileRecipient extends Profile implements MailRecipientInterface
{
    use MailRecipient;

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->email;
    }

    /**
     * Returns an array of mail receiver variables, that are can be used in the mail
     * @return array
     */
    public function getRecipientVariables()
    {
        return [
            '{email}' => $this->email,
            '{firstName}' => $this->first_name,
            '{lastName}' => $this->last_name,
        ];
    }
}