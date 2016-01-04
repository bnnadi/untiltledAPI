<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 3:04 PM
 */

namespace cms\core\dao;


class RoleDAO
{
    function __construct()
    {
        parent::__construct();
        $this->model = '\cms\core\model\Role';
        $this->table = 'roles';
        $this->id_column = 'role_id';
        $this->table_fields = array('role_id', 'role_name');
    }
}