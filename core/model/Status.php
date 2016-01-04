<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 3:39 PM
 */

namespace cms\core\model;


class Status extends Model
{
    var $status_id;
    var $status_category;
    var $status_name;
    function __construct()
    {
        parent::__construct();
        $this->dao = new \cms\core\dao\StatusDAO;
    }
    function getValidationMessages()
    {
        $messages = array();
        if (strlen($this->status_category) <= 0)
            $messages['status_category'] = 'Status Category must be set.';
        if (strlen($this->status_name) <= 0)
            $messages['status_name'] = 'Status Name must be set.';
        return $messages;
    }
}