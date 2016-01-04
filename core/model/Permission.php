<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 3:39 PM
 */

namespace cms\core\model;


class Permission extends Model
{
    var $permission_id;
    var $permission_controller;
    var $permission_action;
    var $permission_description;
    function __construct()
    {
        $this->dao = new \cms\core\dao\PermissionsDAO();
    }
    function getValidationMessages()
    {
        return array();
    }
    function getRolePermissions($user_role)
    {
        return $this->dao->getUserRolePermissions($user_role);
    }
}