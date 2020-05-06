<?php 

namespace api\models;

class ErrorCodeMessage {

    const USER_INDEX_NOT_ADMIN = ['code' => 10, 'msg' => 'Current logged user don\'t have permission to list users, it\'s not an admin'];
    const USER_CREATE_NOT_ADMIN = ['code' => 20, 'msg' => 'Current logged user don\'t have permission to create users, it\'s not an admin'];
    const USER_UPDATE_NOT_ADMIN = ['code' => 30, 'msg' => 'Current logged user don\'t have permission to update another user but himself, it\'s not an admin'];
    const USER_DELETE_NOT_ADMIN = ['code' => 40, 'msg' => 'Current logged user don\'t have permission to delete users, it\'s not an admin'];
    const USER_CANT_CHANGE_ANOTHER_USER_PASSWORD = ['code' => 50, 'msg' => 'You can\'t change another\'s user password'];

}