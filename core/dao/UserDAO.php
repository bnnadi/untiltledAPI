<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 3:00 PM
 */

namespace cms\core\dao;


class UserDAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'users';
        $this->id_column = 'user_id';
        $this->model = '\bisikecms\core\model\User';
        $this->table_fields = array('user_contact_id', 'user_name', 'user_auth_token', 'user_auth_token_date', 'user_password',
            'user_password_hash_date', 'user_salt', 'user_created_date', 'user_modified_date', 'user_status_id', 'user_visible', 'user_role_id', 'user_user_type_id','user_reset_token','user_reset_date');
        $this->visible_field = 'user_visible';
        $this->addHasOne('contacts', 'contact_id', 'user_contact_id');
        $this->addHasOne('statuses', 'status_id', 'user_status_id');
        $this->addHasOne('user_types', 'user_type_id', 'user_user_type_id');
    }
    function getUsersForUsername($username)
    {
        $query = "SELECT * FROM {$this->table} LEFT JOIN contacts ON {$this->table}.user_contact_id = contacts.contact_id WHERE {$this->table}.user_name = :user_name";
        $params = array(':user_name'=>$username);
        $users = $this::getConnection()->query($query, $params);
        return $users;
    }
    function getUserForAuthToken($user_auth_token)
    {
        $query = "SELECT * FROM {$this->table} LEFT JOIN contacts ON {$this->table}.user_contact_id = contacts.contact_id WHERE {$this->table}.user_auth_token = :user_auth_token";
        $params = array(':user_auth_token'=>$user_auth_token);
        $user = $this::getConnection()->queryOne($query, $params);
        return $user;
    }
    function getUserForReset($user_reset_token)
    {
        $query = "SELECT * FROM {$this->table} LEFT JOIN contacts ON {$this->table}.user_contact_id = contacts.contact_id WHERE {$this->table}.user_reset_token = :user_reset_token";
        $params = array(':user_reset_token'=>$user_reset_token);
        $user = $this::getConnection()->queryOne($query, $params);
        return $user;
    }
    function getUserPermissions($user_id)
    {
        $query = "SELECT permissions.* FROM users_permissions INNER JOIN permissions ON users_permissions.user_permission_permission_id = permissions.permission_id  WHERE users_permissions.user_permission_user_id = :user_id";
        $params = array(':user_id'=>$user_id);
        $user = $this::getConnection()->query($query, $params);
        return $user;
    }
    function getUserPermissionByController($user_id, $controller)
    {
        $controller = explode('/',$controller);
        $query = "SELECT * FROM users_permissions LEFT JOIN permissions ON users_permissions.user_permission_permission_id = permissions.permission_id
                  WHERE users_permissions.user_permission_user_id = :user_id AND permissions.permission_controller = :controller AND permissions.permission_action = :actions";
        $params = array(':user_id'=>$user_id, ':controller'=>$controller[0], ':actions'=>$controller[1]);
        $user = $this::getConnection()->query($query, $params);
        return $user;
    }
}