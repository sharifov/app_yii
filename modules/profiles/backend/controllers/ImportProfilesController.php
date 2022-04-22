<?php

namespace modules\profiles\backend\controllers;

use backend\base\Controller;
use libphonenumber\PhoneNumberFormat;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\SalesPoint;
use ms\loyalty\bonuses\sales\common\models\Category;
use ms\loyalty\bonuses\sales\common\models\Product;
use marketingsolutions\phonenumbers\PhoneNumber;
use yz\admin\import\BatchImportAction;
use yz\admin\import\ImportForm;
use yz\admin\import\InterruptImportException;

/**
 * Class ImportProfilesController
 */
class ImportProfilesController extends Controller
{
    const FIELD_FIRST_NAME = 'имя';
    const FIELD_LAST_NAME = 'фамилия';
    const FIELD_PHONE = 'телефон';
    const FIELD_EMAIL = 'email';
    const FIELD_DEALER = 'дилер';
    const FIELD_ROLE = 'должность';

    public function actions()
    {
        return [
            'index' => [
                'class' => BatchImportAction::className(),
                'extraView' => '@modules/profiles/backend/views/import-profiles/partials/_profiles.php',
                'importConfig' => [
                    'availableFields' => [
                        self::FIELD_FIRST_NAME => 'Имя участника',
                        self::FIELD_LAST_NAME => 'Фамилия участника',
                        self::FIELD_PHONE => 'Номер телефона',
                        self::FIELD_EMAIL => 'Электронная почта участника',
                        self::FIELD_DEALER => 'Дилер',
                        self::FIELD_ROLE => 'Должность',
                    ],
                    'rowImport' => [$this, 'rowImport'],
                ]
            ]
        ];
    }

    public function rowImport(ImportForm $form, array $row)
    {
        $dealer = $this->importDealer($row);
        $profile = $this->importProfile($row, $dealer);
    }

    public function importDealer(array $row)
    {
        $dealer = Dealer::findOne(['name' => $row[self::FIELD_DEALER]]);

        if ($dealer === null) {
            $dealer->name = $row[self::FIELD_DEALER];

            if ($dealer->save() === false) {
                throw new InterruptImportException('Ошибка при импорте РТТ: ' . implode(', ', $dealer->getFirstErrors()), $row);
            }
        }

        return $dealer;
    }

    private function importProfile(array $row, Dealer $dealer)
    {
        if (PhoneNumber::validate($row[self::FIELD_PHONE], 'RU') == false) {
            throw new InterruptImportException('Неверный номер телефона: ' . $row[self::FIELD_PHONE], $row);
        }

        $profile = Profile::findOne([
            'phone_mobile' => PhoneNumber::format($row[self::FIELD_PHONE], PhoneNumberFormat::E164, 'RU')
        ]);

        if ($profile === null) {
            $profile = new Profile();
            $profile->first_name = $row[self::FIELD_FIRST_NAME];
            $profile->last_name = $row[self::FIELD_LAST_NAME];
            $profile->phone_mobile_local = $row[self::FIELD_PHONE];
            $profile->email = $row[self::FIELD_EMAIL];
            $profile->dealer_id = $dealer->id;

            if (isset(array_flip(Profile::getRoleValues())[$row[self::FIELD_ROLE]])) {
                $profile->role = array_flip(Profile::getRoleValues())[$row[self::FIELD_ROLE]];
            }

            if ($profile->save() == false) {
                throw new InterruptImportException('Ошибка при импорте участника: ' . implode(', ', $profile->getFirstErrors()), $row);
            }
        }

        return $profile;

    }
}