<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:22 PM
 */

namespace cms\core\controller;


use cms\core\model\ApiLog;
use cms\core\model\Site;
use cms\core\model\User;
use cms\core\utilities\Response;

class Controller
{
    var $cms;
    var $response;
    var $user;
    var $site;
    var $api_log;
    function __construct()
    {
        $this->response = new Response();
        $this->user = new User();
        $this->site = new Site();
        $this->api_log = new ApiLog();
    }
    function initialize($cms, $user, $site)
    {
        $this->cms = $cms;
        $this->user = $user;
        $this->site = $site;
        $this->api_log->api_log_user_serialized = serialize(array('user_id'=>$user->user_id, 'user_auth_token'=>$user->user_auth_token));
        $this->api_log->api_log_get_serialized = serialize($cms->getGet());
        $this->api_log->api_log_post_serialized = serialize($cms->getPost());
        $this->api_log->api_log_files_serialized = serialize($cms->getFiles());
        $this->api_log->api_log_server_serialized = serialize($cms->getServer());
        $this->api_log->create();
    }
    function respond()
    {
        $this->api_log->api_log_response_serialized = serialize($this->response);
        $this->api_log->update();
        echo json_encode(array(	'success'=>$this->response->getStatus(),
            'data'=>$this->response->getData(),
            'messages'=>$this->response->getMessages()));
    }
}