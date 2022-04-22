<?php

namespace modules\profiles\common\models;
use ms\loyalty\contracts\profiles\ProfileFinderInterface;
use ms\loyalty\contracts\profiles\ProfileInterface;


/**
 * Class ProfileFinder
 */
class ProfileFinder implements ProfileFinderInterface
{

    /**
     * @param $id
     * @return ProfileInterface | null
     */
    public function findByIdentityId($id)
    {
        return Profile::findOne(['identity_id' => $id]);
    }

    /**
     * @param $id
     * @return ProfileInterface | null
     */
    public function findByProfileId($id)
    {
        return Profile::findOne($id);
    }

    /**
     * Find profile by the given attributes
     * @param array $attributes
     * @return ProfileInterface | null
     */
    public function findByAttributes($attributes)
    {
        return Profile::findOne($attributes);
    }
}