<?php

namespace modules\news\frontend\controllers\api;

use modules\profiles\common\models\Profile;
use Yii;
use yii\db\Expression;
use ms\loyalty\api\frontend\base\ApiController;
use modules\news\frontend\models\ApiNews;
use modules\profiles\frontend\models\ApiProfile;

/**
 * @apiDefine NewsNotFoundError
 * @apiError NewsNotFound Новость не найдена
 * @apiErrorExample {json} Пример ошибки "Новость не найдена":
 * HTTP/1.1 400 Bad Request
 * {
 *     "result": "FAIL",
 *     "errors": {
 *       "news": "Новость не найдена"
 *     }
 * }
 */

/**
 * Class NewsController
 */
class NewsController extends ApiController
{
    /**
     * @api {post} news/api/news/list Список новостей
     * @apiDescription Получить список всех новостей участника
     * @apiName NewsGetList
     * @apiGroup News
     *
     * @apiParam {Number} profile_id Идентификатор профиля участника
     * @apiParamExample {json} Пример запроса:
     * {
     *   "profile_id": 1
     * }
     *
     * @apiSuccess {String} result Статус ответа "OK"
     * @apiSuccess {Object[]} news Список новостей
     * @apiSuccess {Number} news.id Идентификатор новости
     * @apiSuccess {String} news.title Заголовок новости
     * @apiSuccess {String} news.content HTML содержимое новости
     * @apiSuccess {String} news.created_at Дата создания
     * @apiSuccessExample {json} Пример успешного ответа:
     * HTTP/1.1 200 OK
     * {
     *   "result": "OK",
     *   "news": [
     *     {
     *       "id": 3,
     *       "title": "Новая тестовая акция",
     *       "content": "<p>Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться. Lorem Ipsum используют потому, что тот </p>",
     *       "created_at": "13.05.2019"
     *     },
     *     {
     *       "id": 2,
     *       "title": "Это тестовая новость",
     *       "content": "<p><strong>Lorem Ipsum</strong>&nbsp;- это текст-\"рыба\", часто используемый в печати и вэб-дизайне. </p>",
     *       "created_at": "13.05.2019"
     *     }
     *   ]
     * }
     *
     * @apiUse ProfileNotFoundError
     * @apiUse ServerUnknownError
     */
    public function actionList()
    {
        try {
            $profile_id = Yii::$app->request->post('profile_id');

            /** @var ApiProfile $profile */
            $profile = Profile::findOne(['id' => $profile_id]);


            if (null === $profile) {
                return $this->error('Участник не найден', 'Ошибка при получении списка новостей');
            }

            $news = ApiNews::find()
                ->where(['enabled' => true])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            return $this->ok(['news' => $news], 'Выдача списка новостей');
        }
        catch (\Throwable $e) {
            return $this->error(['server' => 'Ошибка на сервере'.$e->getMessage()], 'Ошибка на сервере: ' . $e->getMessage(), 500);
        }
    }
}