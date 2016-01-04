<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 3:40 PM
 */

namespace cms\core\dao;


class PermissionsDAO extends DAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'permissions';
        $this->id_column = 'permission_id';
        $this->table_fields = array('permission_id', 'permission_controller','permission_action','permission_description');
        $this->model = '\bisikecms\core\model\Permission';
    }
    function getUserRolePermissions($user_role)
    {
        $query = "	SELECT permissions.* FROM roles_permissions
					LEFT JOIN permissions ON roles_permissions.role_permission_permission_id = permissions.permission_id
					WHERE roles_permissions.role_permission_role_id = :user_role";
        $params = array(':user_role'=>$user_role);
        return $this::getConnection()->query($query, $params);
    }
}