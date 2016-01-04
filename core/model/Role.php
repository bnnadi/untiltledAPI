<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:32 PM
 */

namespace cms\core\model;


use cms\core\dao\RoleDAO;

class Role extends Model
{
    var $role_id;
    var $role_name;
    function __construct()
    {
        $this->dao = new RoleDAO();
    }
    function getValidationMessages()
    {
        return array();
    }
}