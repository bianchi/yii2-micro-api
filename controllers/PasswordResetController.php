<?php 

namespace api\controllers;

use api\models\PasswordReset;
use api\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
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

        $model->checkValid();

        return $model;
    }

    public function actionChangePassword($id)
    {
        $passwordReset = PasswordReset::findOne($id);
        $passwordReset->checkValid();

        $body = \Yii::$app->getRequest()->getBodyParams();
        if (empty($body['password'])) {
            throw new BadRequestHttpException("Missing body parameter 'password'");
        }

        $user = User::findOne($passwordReset->user_id);
        if ($user == null) {
            throw new NotFoundHttpException("User not found");
        }

        $user->password = password_hash($body['password'], PASSWORD_BCRYPT);
        $user->updateAttributes(['password']);

        $passwordReset->already_used = true;
        $passwordReset->updateAttributes(['already_used']);

        \Yii::$app->response->statusCode = 204;
    }
}