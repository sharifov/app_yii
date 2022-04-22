<?php

namespace modules\news\frontend\models;

use modules\news\common\models\News;
use modules\profiles\frontend\models\ApiBrand;

class ApiNews extends News
{
    public function fields()
    {
        return [
            'id',
            'title',
            'content',
            'created_at' => function (ApiNews $model) {
                if (empty($model->created_at)) {
                    return null;
                }

                if ($model->created_at == 'NOW()') {
                    return (new \DateTime)->format('d.m.Y');
                }

                return (new \DateTime($model->created_at))->format('d.m.Y');
            }
        ];
    }


    /**
     * Добавление признака прочтения новостей (В поле is_read содержится сериализованный массив с id участников просмотревших новость)
     * @param integer $profile_id
     * @param integer $news_id
     * @return bool
     * @throws \Throwable
     */
    public static function setPublicationReaded(int $profile_id, int $news_id){
        $model = News::findOne(['id' => $news_id]);

        if (null === $model) {
            return false;
        }

        $readingUserId[] = $profile_id;

        if ($model->is_read == null || $model->is_read == ''){
            $arrReadingUser = [];
        } else{
            $arrReadingUser = unserialize($model->is_read);
        }

        if (!in_array($profile_id, $arrReadingUser)) {
            $newArrUser = array_merge($arrReadingUser, $readingUserId);
         }else{
            $newArrUser = $arrReadingUser;
        }

        $model->is_read = serialize($newArrUser);
        $model->update(false);

        return true;
    }
}