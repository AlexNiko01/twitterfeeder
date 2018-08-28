<?php

namespace app\controllers;

use Yii;
use app\models\TwitterUser;
use yii\base\Module;
use yii\web\Controller;
use yii\filters\VerbFilter;
use \yii\web\Response;

/**
 * TwitterController implements the CRUD actions for TwitterUser model.
 */
class TwitterController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['GET'],
                    'feed' => ['GET'],
                    'remove' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * TwitterController constructor.
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, array $config = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return mixed
     */
    private function accessValidator()
    {
        $request = Yii::$app->request;
        $response = [];
        $id = $request->get('id');
        $user = $request->get('user');
        $secret = $request->get('secret');
        if (!$id || !$user || !$secret) {
            $response['error'] = 'missing parameter';
        };
        if (sha1($id . $user) != $secret) {
            $response['error'] = 'access denied';
        }

        if (strlen($id) < 32) {
            $response['error'] = 'wrong id format';
        }
        return $response;
    }

    /**
     * This method intended for add another Twitter user to the feed
     * @return mixed
     */
    public function actionAdd(): array
    {
        $response = $this->accessValidator();
        if (isset($response['error'])) {
            return $response;
        }
        $request = Yii::$app->request;
        $user = $request->get('user');

        $existedUsers = TwitterUser::find()->where(['user' => $user])->select('user')->asArray()->all();
        if (!empty($existedUsers)) {
            $response['error'] = 'this user already added';
            return $response;
        }

        $twitterUser = new TwitterUser();
        $twitterUser->user = $user;
        $twitterUser->save();
        return [];
    }

    /**
     * This method builds an answer in array format
     * @param $tweets
     * @param $user
     * @return array
     */
    private function buildResponse(array $tweets, string $user): array
    {
        $response = [];
        foreach ($tweets as $tweet) {
            $response[] = [
                'user' => $user,
                'tweet' => $tweet['text'],
                'hashtag' => $tweet['entities']['hashtags']
            ];
        }
        return $response;
    }

    /**
     * This method gets the latest tweets from users that were added and outputs them in specified format
     * @return array|mixed
     */
    public function actionFeed()
    {
        $request = Yii::$app->request;
        $response = [];
        $id = $request->get('id');
        $secret = $request->get('secret');
        if (!$id || !$secret) {
            $response['error'] = 'missing parameter';
        };
        if (sha1($id) != $secret) {
            $response['error'] = 'access denied';
        }
        /**
         * @var $twitter \naffiq\twitterapi\TwitterAPI
         */
        $twitter = \Yii::$app->get('twitter');
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';
        $twitterUsers = TwitterUser::find()->select(['user'])->asArray()->all();
        $feed = [];
        foreach ($twitterUsers as $twitterUser) {
            $postFields = [
                'screen_name' => $twitterUser['user']
            ];

            $tweets = json_decode($twitter->buildOauth($url, $requestMethod)
                ->setPostfields($postFields)
                ->performRequest(), true);
            if (!empty($tweets['errors'])) {
                return $tweets;
            }
            $response = $this->buildResponse($tweets, $twitterUser['user']);
            $feed[] = $response;
        }
        return ['feed' => $feed];
    }

    /**
     * This method removes already added users from the list of users
     * @return array|mixed
     */
    public function actionRemove()
    {
        $response = $this->accessValidator();
        if (!isset($response['error'])) {
            return $response;
        }
        $request = Yii::$app->request;
        $id = $request->get('id');

        $twitterUser = TwitterUser::findOne(['src_id' => $id]);
        if (!$twitterUser) {
            $response['error'] = 'internal error';
            return $response;
        }
        $twitterUser->delete();
        return [];
    }

}
