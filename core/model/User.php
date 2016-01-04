<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:31 PM
 */

namespace cms\core\model;


use cms\core\model\Role;
use cms\core\utilities\Emailer;
use cms\core\utilities\Security;
const CMS_ADMIN = 1;
const CMS_USER = 2;
const API_USER = 3;
const SITE_USER = 4;

class User extends Model
{
    var $user_id;
    var $user_contact_id;
    var $user_name;
    var $user_auth_token;
    var $user_auth_token_date;
    var $user_password;
    var $user_password_hash_date;
    var $user_salt;
    var $user_created_date;
    var $user_modified_date;
    var $user_role_id;
    var $user_user_type_id;
    var $user_status_id;
    var $user_visible;
    var $user_reset_token;
    var $user_reset_date;
    var $contact;
    var $status;
    function __construct()
    {
        parent::__construct();
        $this->dao = new \cms\core\dao\UserDAO();
        $this->user_id = 0;
        $this->user_contact_id = 0;
        $this->user_name = '';
        $this->user_auth_token = '';
        $this->user_auth_token_date = 0;
        $this->user_password = '';
        $this->user_password_hash_date = '';
        $this->user_salt = '';
        $this->user_created_date = 0;
        $this->user_modified_date = 0;
        $this->user_role_id = 0;
        $this->user_user_type_id = 0;
        $this->user_status_id = 1;
        $this->user_visible = 1;
        $this->user_reset_token = '';
        $this->user_reset_date = 0;
        $this->contact = new \cms\core\model\Contact();
        $this->status = new \cms\core\model\Status();
        $this->create_success_message = 'User successfully saved.';
        $this->update_success_message = 'User successfully updated.';
        $this->create_failure_message = 'User failed to be saved.';
        $this->update_failure_message = 'User failed to be updated';
    }
    private function createAuthToken()
    {
        $security = new Security();
        $this->user_auth_token = $security->hash($this->user_password, $this->user_salt . time());
        $this->user_auth_token_date = date('Y-m-d G:i:s', time());
    }
    private function createForgotPassToken()
    {
        $security = new Security();
        $this->user_reset_token = $security->hash($this->user_password, $this->user_salt . time());
        $this->user_reset_date = date('Y-m-d G:i:s', time());
    }
    function check()
    {
        if($this->user_user_type_id <= API_USER && $this->user_user_type_id!= 0 ){
            return true;
        }
        return false;
    }
    function read($id)
    {
        $read = parent::read($id);
        $this->contact->setFieldsFromArray($read);
    }
    function hashPassword()
    {
        $security = new Security;
        $this->user_salt = $security->generateSalt();
        $this->user_password = $security->hash($this->user_password, $this->user_salt);
    }
    function create()
    {
        $response = $this->contact->create();
        $this->user_contact_id = $this->contact->contact_id;
        $this->createAuthToken();
        $user_response = parent::create();
        if ($user_response->getStatus())
            $response->merge($user_response);
        else
        {
            $this->contact->loseHistoryDelete($this->contact);
            $response = $user_response;
        }
        return $response;
    }
    function update()
    {
        $response = $this->contact->update();
        $this->user_contact_id = $this->contact->contact_id;
        $user_response = parent::update();
        if ($user_response->getStatus() > 0)
            $response->merge($user_response);
        else
            $response = $user_response;
        return $response;
    }
    function authenticate()
    {
        $users = $this->dao->getUsersForUsername($this->user_name);
        $security = new Security();
        foreach($users AS $user)
        {
            if ($user['user_password'] == $security->hash($this->user_password, $user['user_salt']))
            {
                $this->setFieldsFromArray($user);
                $this->createAuthToken();
                if ($this->update()->getStatus() > 0)
                    return true;
            }
        }
        return false;
    }
    function hasPermission($permission_check)
    {
        $role = new Role();
        $role->role_id = $this->user_role_id;
        return $role->doesRoleHavePermission($permission_check);
    }
    private function isAuthTokenValid()
    {
        if (strtotime($this->user_auth_token_date) > (time() - (60*60*24*7*2)))
            return true;
        return false;
    }
    function setFieldsFromArray($array)
    {
        $this->contact->setFieldsFromArray($array);
        parent::setFieldsFromArray($array);
    }
    function getValidationMessages()
    {
        $messages = $this->contact->getValidationMessages();
        if (strlen($this->user_name) < 3)
            $messages['user_name'] = 'User name must be at least 3 characters.';
        else if (strlen($this->user_name) > 255)
            $messages['user_name'] = 'User name cannot be longer than 255 characters.';
        else if ($this->hasDuplicate(array('user_name'=>$this->user_name), $this->user_id))
            $messages['user_name'] = 'User name is already in use.';
        if (strlen($this->user_password) <= 0)
            $messages['user_password'] = 'User Password must be set.';
        elseif(strlen($this->user_password) < 8)
            $messages['user_password'] = 'User Password must be at least 8 characters.';
        return $messages;
    }
    function getAllUserPermissions()
    {
        return $this->dao->getUserPermissions($this->user_id);
    }
    function getUserPermission($controller)
    {
        $permission = $this->dao->getUserPermissionByController($this->user_id,$controller);
        return $this->hasPermission($permission->user_permission_permission_id);
    }
    function authenticateWithAuthToken()
    {
        if (strlen($this->user_auth_token) > 0)
        {
            $user = $this->dao->getUserForAuthToken($this->user_auth_token);
            if ($user)
            {
                $this->setFieldsFromArray($user);
                if ($this->isAuthTokenValid())
                    return true;
            }
        }
        return false;
    }
    function forgot()
    {
        $users = $this->dao->getUsersForUsername($this->user_name);
        if(count($users) > 0){
            $forgot = new Emailer();
            $this->setFieldsFromArray($users[0]);
            $data = array(
                '::FIRST_NAME'=>$this->contact->contact_first_name,
                '::LAST_NAME'=>$this->contact->contact_last_name
            );
            $this->createForgotPassToken();
            $this->update();
            if($this->user_user_type_id != 3 && $this->user_user_type_id != 4){
                $data['::PASSWORD_RECOVERY'] = CMS_URL.'/forgot-password/'.$this->user_reset_token;
            } else {
                $data['::PASSWORD_RECOVERY'] = SITE_URL.'/forgot-password/'.$this->user_reset_token;
            }
            ob_start();
            include(CMS_TEMPLATES.'mailers/forgot_password.php');
            $forgot->template = ob_get_contents();
            ob_end_clean();
            $forgot->setData($data);
            $forgot->setSubject('Forgot Your password?');
            $forgot->addTo($this->contact->contact_email,$this->contact->contact_first_name.' '.$this->contact->contact_last_name );
            $forgot->setFrom('support@bisikennadi.com',"Bisike's Site Support");
            $forgot->send();
            return true;
        }
        return false;
    }
    function reset()
    {
        $password = $this->user_password;
        $user = $this->dao->getUserForReset($this->user_reset_token);
        if (count($user)>0) {
            $user['user_password'] = $password;
            $this->setFieldsFromArray($user);
            $this->hashPassword();
            $this->createAuthToken();
            $this->update();
            $reset = new Emailer();
            $data = array(
                '::FIRST_NAME'=>$user['contact']['contact_first_name'],
                '::LAST_NAME'=>$user['contact']['contact_last_name']
            );
            if($user['user_user_type_id'] != 3 && $user['user_user_type_id'] != 4){
                $data['::LOGIN'] = CMS_URL.'/';
            } else {
                $data['::LOGIN'] = SITE_URL.'/forgot-password/';
            }
            ob_start();
            include(CMS_TEMPLATES.'/mailers/password_reset.php');
            $reset->template = ob_get_contents();
            ob_end_clean();
            $reset->setData($data);
            $reset->setSubject('Password has been reset');
            $reset->addTo($user['contact']['contact_email'],$user['contact']['contact_first_name'].' '.$user['contact']['contact_last_name']);
            $reset->setFrom('support@bisikennadi.com',"Bisike's Site Support");
            $reset->send();
            return $this->authenticateWithAuthToken();
        }
    }
}