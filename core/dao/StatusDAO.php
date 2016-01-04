<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 3:41 PM
 */

namespace cms\core\dao;


class StatusDAO extends DAO
{
    function __construct()
    {
        parent::__construct();
        $this->id_column = 'status_id';
        $this->table_fields = array('status_category', 'status_name');
        $this->table = 'statuses';
    }
}