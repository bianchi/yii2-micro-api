<?php 

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\search\UserSearch;
use api\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;

class UserController extends BaseController
{
    public $modelClass = 'api\models\User';

    /**
     * Checks the privilege of the current user.
     *
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action === 'index' && !$user->is_admin) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to list users, it\'s not an admin');
        }

        if ($action === 'create' && !$user->is_admin) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to create users, it\'s not an admin');
        }

        if ($action === 'delete' && !$user->is_admin) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to delete users, it\'s not an admin');
        }

        if ($action == 'update') {
            if (!$user->is_admin && $model != null && $model->id != \Yii::$app->user->id) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to update another user but himself, it\'s not an admin');
            }
        }
    }

    public function actionLogin()
    {
        $body = \Yii::$app->getRequest()->getBodyParams();
        if (empty($body['email'])) {
            throw new BadRequestHttpException("Missing body parameter 'email'");
        }

        if (empty($body['password'])) {
            throw new BadRequestHttpException("Missing body parameter 'password'");
        }

        $user = User::findOne(['email' => $body['email']]);

        if ($user != null && password_verify($body['password'], $user->password)) {
            $user->scenario = User::SCENARIO_LOGIN;

            $token = preg_replace("/[^a-zA-Z0-9]/", "", \Yii::$app->security->generateRandomString());

            $currentDateTime = date('Y-m-d H:i:s');
            $user->updateAttributes([
                'access_token' => $token,
                'last_login' => $currentDateTime,
                'last_api_request' => $currentDateTime,
            ]);

            return $user;
        } else {
            throw new UnauthorizedHttpException('User doesn\'t exist or password is incorrect');
        }
    }

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new UserSearch;

        return $searchModel->search(\Yii::$app->request->get());
    }

    public function actionCreate() 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        $this->checkAccess($this->action->id);

        $model = new User();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $deletedUser = User::findOne(['email' => $model->email, 'deleted' => true]);
        if ($deletedUser != null) {
            $deletedUser->setAttributes($model->attributes());
            $model = $deletedUser;
        }

        $model->customer_id = $loggedUser->customer_id;

        if ($model->save()) {
            $response = \Yii::$app->getResponse()->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['/users/' . $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionView($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        $model = User::findOne(['id' => $id, 'customer_id' => $loggedUser->customer_id, 'deleted' => false]);
        if ($model == null) {
            throw new NotFoundHttpException("User not found");
        }

        $this->checkAccess($this->action->id, $model);
        
        return $model;
    }

    public function actionUpdate($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        $model = User::findOne(['id' => $id, 'customer_id' => $loggedUser->customer_id, 'deleted' => false]);
        if ($model == null) {
            throw new NotFoundHttpException("User not found");
        }
        
        $this->checkAccess($this->action->id, $model);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        $model->password = $model->oldAttributes['password'];
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function actionDelete($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        if ($id == $loggedUser->id) {
            throw new ForbiddenHttpException('You cannot delete yourself');
        }

        $model = User::findOne(['id' => $id, 'customer_id' => $loggedUser->customer_id, 'deleted' => false]);
        if ($model == null) {
            throw new NotFoundHttpException("User not found");
        }

        $this->checkAccess($this->action->id, $model);

        $model->deleted = true;
        if (!$model->save()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        \Yii::$app->getResponse()->setStatusCode(204);
    }
}