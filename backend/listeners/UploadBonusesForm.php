<?php

namespace backend\listeners;
use libphonenumber\PhoneNumberFormat;
use ms\loyalty\bonuses\lists\backend\events\FindProfileEvent;
use ms\loyalty\contracts\profiles\ProfileFinderInterface;
use marketingsolutions\phonenumbers\PhoneNumber;
use yz\admin\import\InterruptImportException;
use yz\admin\import\SkipRowException;


/**
 * Class UploadBonusesForm
 */
class UploadBonusesForm 
{
    public static function whenFindProfile(FindProfileEvent $event)
    {
        /** @var ProfileFinderInterface $finder */
        $finder = \Yii::$container->get(ProfileFinderInterface::class);
        $event->profile = $finder->findByAttributes(['phone_mobile' => self::formatAttribute($event->attribute)]);
    }

    protected static function formatAttribute($attribute)
    {
        if (PhoneNumber::validate($attribute, 'RU') == false) {
            throw new SkipRowException('Номер телефона имеет неверный формат: '.$attribute);
        }

        return PhoneNumber::format($attribute, PhoneNumberFormat::E164, 'RU');
    }
}