<?php

namespace modules\profiles\common\mailing;
use yz\admin\mailer\common\mailing\MailingListInterface;
use yz\admin\mailer\common\mailing\MailRecipientInterface;
use yii\base\Model;
use modules\profiles\common\models\Profile;


/**
 * Class ProfileMailingList
 */
class ProfileMailingList implements MailingListInterface
{

    /**
     * Title for the current mail list
     * @return string
     */
    public static function listTitle()
    {
        return 'Участники акции';
    }

    /**
     * Mail list data that should be stored into the database
     * @return array
     */
    public function listData()
    {
        return [];
    }

    /**
     * Path for the view
     * @return string
     */
    public static function formView()
    {
        return '@modules/profiles/common/mailing/views/profile.php';
    }

    /**
     * @return \Iterator|MailRecipientInterface[]
     */
    public function getRecipients()
    {
        return ProfileRecipient::find()->each();
    }
}