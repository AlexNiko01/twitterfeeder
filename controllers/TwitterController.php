<?php

namespace app\controllers;

use Yii;
use app\models\TwitterUser;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
     * This method will be used for add another Twitter user to the feed
     * @return mixed
     */
    public function actionAdd(): array
    {
        $response = $this->accessValidator();
        if (!isset($response['error'])) {
            return $response;
        }
        $request = Yii::$app->request;
        $id = $request->get('id');
        $user = $request->get('user');


        $existedUsersId = TwitterUser::find()->where(['src_id' => $id])->select('src_id')->asArray()->all();
        if (!empty($existedUsersId)) {
            $response['error'] = 'this user already added';
            return $response;
        }

        $twitterUser = new TwitterUser();
        $twitterUser->src_id = $id;
        $twitterUser->user = $user;
        $twitterUser->save();
        return [];


    }


    public function actionFeed()
    {

    }


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
