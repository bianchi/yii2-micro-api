<?php

namespace api\controllers;

use api\models\PasswordReset;
use api\models\User;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
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
        $behaviors['authenticator']['except'][] = 'change-password';

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
        $passwordReset->requested_time = date('Y-m-d H:i:s');

        // remove any non alphanumeric characters
        $token = preg_replace("/[^a-zA-Z0-9]/", "", \Yii::$app->security->generateRandomString());

        $expirationTime = new \Datetime;
        $expirationTime->modify('+' . User::PASSWORD_RESET_TOKEN_DURATION_MINUTES . ' minutes');
        $passwordReset->expiration_time = $expirationTime->format('Y-m-d H:i:s');
        $passwordReset->token = $token;
        $passwordReset->already_used = false;

        $transaction = \Yii::$app->db->beginTransaction();
        if ($passwordReset->save()) {
            // delete all non used password reset requested by this user
            PasswordReset::deleteAll(['AND',
                ['!=', 'token', $passwordReset->token],
                ['user_id' => $passwordReset->user_id],
                ['already_used' => false],
            ]);

            $emailSent = \Yii::$app->mailer->compose()
                ->setFrom(['cbrdoc.test@gmail.com' => 'CBR SMB'])
                ->setTo($passwordReset->user->email)
                ->setSubject('Redefinição de senha')
                ->setTextBody('Pare redefinir sua senha <a href="http://google.com" target="_blank">clique aqui</a>')
                ->setHtmlBody('Pare redefinir sua senha <a href="http://google.com" target="_blank">clique aqui</a>')
                ->send();

            if ($emailSent) {
                $response = \Yii::$app->getResponse()->setStatusCode(201);
                $id = implode(',', array_values($passwordReset->getPrimaryKey(true)));
                $response->getHeaders()->set('Location', Url::toRoute(['/password-reset/' . $id], true));

                $transaction->commit();

                return $passwordReset;
            } else {
                $transaction->rollBack();
                \Yii::$app->getResponse()->setStatusCode(502);
                \Yii::$app->getResponse()->content = '';
                return;
            }
        } elseif (!$passwordReset->hasErrors()) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    public function actionView($token)
    {
        $model = PasswordReset::findOne($token);
        if ($model == null) {
            throw new NotFoundHttpException("Token not found");
        }

        return $model;
    }

    public function actionChangePassword($token)
    {
        $passwordReset = PasswordReset::findOne($token);
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
