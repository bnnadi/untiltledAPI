<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:33 PM
 */

namespace cms\core\model;


use cms\core\dao\ApiLogDAO;

class ApiLog extends Model
{
    var $api_log_id;
    var $api_log_user_serialized;
    var $api_log_get_serialized;
    var $api_log_post_serialized;
    var $api_log_files_serialized;
    var $api_log_server_serialized;
    var $api_log_response_serialized;
    var $api_log_date_created;
    var $api_log_date_modified;
    function __construct()
    {
        parent::__construct();
        $this->dao = new ApiLogDAO;
    }
    function getValidationMessages()
    {
        return array();
    }
}