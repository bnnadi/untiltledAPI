<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/5/2016
 * Time: 9:18 AM
 */

namespace cms\core\model;


class Contact extends Model
{
    var $contact_id;
    var $contact_owner_user_id;
    var $contact_contact_type_id;
    var $contact_title;
    var $contact_first_name;
    var $contact_last_name;
    var $contact_email;
    var $contact_company_name;
    var $contact_created_date;
    var $contact_modified_date;
    var $contact_status_id;
    var $contact_visible;
    var $status;
    function __construct()
    {
        parent::__construct();
        $this->dao = new \cms\core\dao\ContactDAO();
        $this->contact_id = 0;
        $this->contact_owner_user_id = 0;
        $this->contact_contact_type_id = 1;
        $this->contact_title = '';
        $this->contact_first_name = '';
        $this->contact_last_name = '';
        $this->contact_email = '';
        $this->contact_company_name = '';
        $this->contact_created_date = 0;
        $this->contact_modified_date = 0;
        $this->contact_status_id = 1;
        $this->contact_visible = 1;
        $this->status = new \cms\core\model\Status;
        $this->create_success_message = 'Contact successfully saved.';
        $this->update_success_message = 'Contact successfully updated.';
        $this->create_failure_message = 'Contact failed to be saved.';
        $this->update_failure_message = 'Contact failed to be updated';
    }
    function getValidationMessages()
    {
        $messages = array();
        if (strlen($this->contact_first_name) <= 0)
            $messages['contact_first_name'] = 'Contact First Name must be set.';
        if (strlen($this->contact_last_name) <= 0)
            $messages['contact_last_name'] = 'Contact Last Name must be set.';
        if (strlen($this->contact_email) <= 0)
            $messages['contact_email'] = 'Contact Email must be set.';
        elseif (!filter_var($this->contact_email, FILTER_VALIDATE_EMAIL))
            $messages['contact_email'] = 'Contact Email is invalid, please check your input.';
        return $messages;
    }

}