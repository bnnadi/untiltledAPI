<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:52 PM
 */

namespace cms\core\dao;


class ApiLogDAO extends DAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'api_logs';
        $this->id_column = 'api_log_id';
        $this->model = '\bisikecms\core\model\ApiLog';
        $this->table_fields = array('api_log_user_serialized', 'api_log_get_serialized', 'api_log_post_serialized', 'api_log_files_serialized', 'api_log_server_serialized', 'api_log_response_serialized', 'api_log_date_created', 'api_log_date_modified');
    }
}