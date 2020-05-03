<?php 

namespace api\controllers;

use api\models\PasswordReset;
use api\models\User;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class PasswordResetController extends BaseController
{
    public $modelClass = 'api\models\PasswordReset';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'][] = 'create';
        $behaviors['authenticator']['except'][] = 'view';

        return $behaviors;
    }

    /**
     * Disable password-reset/index, password-reset/delete, password-reset/update
     * @return array $actions
     */
    public function actions()
    {
        $actions = parent::actions();

        /**
         * disable default "index", "delete" and "update" actions in this endpoint
         * "create" and "view are unseted because we will use the custom ones in this controller
         */
        unset($actions['index'], $actions['delete'], $actions['update'], $actions['create'], $actions['view']);

        return $actions;
    }

    /**
     * Generates a password_reset entry with a token and send an email to the user
     * with the link to reset its password
     *
     * @throws NotFoundHttpException If user doesn't exist
     */
    public function actionCreate()
    {
        $body = \Yii::$app->getRequest()->getBodyParams();

        if (empty($body['email'])) {
            throw new BadRequestHttpException("Missing body parameter 'email'");
        }

        $user = User::findOne(['email' => $body['email']]);
        if ($user == null) {
            throw new NotFoundHttpException("User not found");
        }

        $passwordReset = new PasswordReset;
        $passwordReset->user_id = $user->id;

        // remove any non alphanumeric characters
        $token = preg_replace("/[^a-zA-Z0-9]/", "", \Yii::$app->security->generateRandomString());

        $expirationTime = new \Datetime;
        $expirationTime->modify('+' . User::PASSWORD_RESET_TOKEN_DURATION_MINUTES . ' minutes');
        $passwordReset->expiration_time = $expirationTime->format('Y-m-d H:i:s');
        $passwordReset->token = $token;
        $passwordReset->already_used = false;

        if ($passwordReset->save() === false && !$passwordReset->hasErrors()) {
            throw new ServerErrorHttpException('Failed for unknown reason.', 0);
        }

        \Yii::$app->response->statusCode = 204;
    }

    public function actionView($id) 
    {
        $model = PasswordReset::findOne($id);
        if ($model == null) {
            throw new NotFoundHttpException("Token not found");
        }

        $currentTime = new \Datetime;
        $tokenExpiration = new \Datetime($model->expiration_time);
        if ($tokenExpiration < $currentTime) {
            throw new NotAcceptableHttpException("Token has expired");
        }

        if ($model->already_used) {
            throw new NotAcceptableHttpException("Token already used");
        }

        return $model;
    }
}