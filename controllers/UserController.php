<?php 

namespace api\controllers;

use api\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UserController extends BaseController
{
    public $modelClass = 'api\models\User';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "index" action in this endpoint
        unset($actions['index']);

        return $actions;
    }

    /**
     * Checks the privilege of the current user.
     *
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action === 'delete' && !$user->is_admin) { // TODO replace false with verification if the logged in user has permission
            throw new \yii\web\ForbiddenHttpException('Current logged user don\'t have permission to delete users, it\'s not an admin');
        }
    }

    public function actionLogin()
    {
        $body = \Yii::$app->getRequest()->getBodyParams();

        $user = User::findOne(['email' => $body['email']]);

        if ($user != null && password_verify($body['password'], $user->password)) {
            $user->scenario = User::SCENARIO_LOGIN;

            $currentDateTime = date('Y-m-d H:i:s');
            $user->updateAttributes([
                'access_token' => \Yii::$app->security->generateRandomString(),
                'last_login' => $currentDateTime,
                'last_api_request' => $currentDateTime,
            ]);

            return $user;
        } else {
            throw new ForbiddenHttpException('User doesn\'t exist or password is incorrect.');
        }
    }
}