<?php 

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;

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
            throw new ForbiddenHttpException('Current logged user don\'t have permission to create users, it\'s not an admin');
        }

        if ($action === 'delete' && !$user->is_admin) {
            if ($model->id != $user->customer_id) {
                throw new ForbiddenHttpException('Cannot delete users from another customer');
            }

            throw new ForbiddenHttpException('Current logged user don\'t have permission to delete users, it\'s not an admin');
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
     *  @OA\Post(
     *      path="/users/login",
     *      summary="Check user email/password, if ok generates an access_token.",
     *      tags={"users"},
     *      @OA\RequestBody(
     *         description="Request body",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="User's email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User's password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200,description="User object with the access_token"),
     *     @OA\Response(response=400,description="Missing body parameters"),
     *     @OA\Response(response=403,description="User doesn\'t exist or password is incorrect")
     *  )
     */
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
            throw new ForbiddenHttpException('User doesn\'t exist or password is incorrect');
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


     /**
     *  @OA\Get(
     *     path="/users/{user_id}",
     *     summary="Gets specific user information",
     *     tags={"users"},
     *     @OA\Parameter(name="user_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200,description="User object"),
     *     @OA\Response(response=403,description="Users cannot view users from another customer \n ahsduhasd")
     *  )
     */
    public function actionView() {}

    /**
     *  @OA\Post(
     *     path="/users",
     *     summary="Creates a new user",
     *     tags={"users"},
     *      @OA\RequestBody(
     *         description="Request body",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="customer_id",
     *                     description="ID of the customer whom the user will belong",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="User's name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     description="User's phone",
     *                     type="integer",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User's email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User's password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_admin",
     *                     description="Whether user is an admin",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="can_order_document",
     *                     description="Whether user can order documents/searchs",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="can_insert_credits",
     *                     description="Whether user can insert credits",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="can_see_reports",
     *                     description="Whether user can see reports",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="can_see_invoices",
     *                     description="Whether user can see invoices",
     *                     type="boolean"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200,description="User object"),
     *     @OA\Response(response=403,description="Users cannot view users from another customer")
     *  )
     */
    public function actionCreate() {}

}