<?php 

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\User;
use app\models\PasswordReset;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends BaseController
{
    public $modelClass = 'api\models\User';

    /**
     * Disable user/index 
     * @return array $actions
     */
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
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action === 'create' && !$user->is_admin) {
            throw new \yii\web\ForbiddenHttpException('Current logged user don\'t have permission to create users, it\'s not an admin');
        }

        if ($action === 'delete' && !$user->is_admin) {
            throw new \yii\web\ForbiddenHttpException('Current logged user don\'t have permission to delete users, it\'s not an admin');
        }

        if ($action == 'view') {
            if ($model->id != $user->customer_id) {
                throw new ForbiddenHttpException('Users cannot view users from another customer');
            }
        }

        if ($action == 'update') {
            if ($model->id != $user->customer_id) {
                throw new ForbiddenHttpException('Users cannot update users from another customer');
            }

            if (!$user->is_admin && $model != null && $model->id != \Yii::$app->user->id) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to update another user but himself, it\'s not an admin');
            }
        }

        if ($action == 'orders' && !$user->is_admin &&  $params['user_id'] != \Yii::$app->user->id) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to list orders from another users, it\'s not an admin');
        }
    }

     /**
     * Check user email/password, if ok generates an access_token
     *
     * @return User $user with access_token
     * @throws ForbiddenHttpException If user doesn't exist or password is wrong
     */
    public function actionLogin()
    {
        $body = \Yii::$app->getRequest()->getBodyParams();

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
            throw new ForbiddenHttpException('User doesn\'t exist or password is incorrect.');
        }
    }

     /**
     * List orders from $user_id user
     *
     * @param int $user_id the ID of the user to search orders
     * @return Order[] from that user
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionOrders($user_id)
    {
        $this->checkAccess($this->action->id, null, ['user_id' => $user_id]);

        $searchModel = new OrderSearch();    
        
        return $searchModel->search(\Yii::$app->request->get());
    }
}