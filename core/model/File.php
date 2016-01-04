<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:37 PM
 */

namespace cms\core\model;


use cms\core\dao\FileDAO;
use cms\core\model\FileType;

class File extends Model
{
    var $file_id;
    var $file_user_id;
    var $file_name;
    var $file_display_name;
    var $file_parent_file_id;
    var $file_culture_id;
    var $file_size;
    var $file_file_type_id;
    var $file_created_date;
    var $file_modified_date;
    var $file_visible;
    var $file_status_id;
    var $file_mime_type;
    var $file_image_alt_tag;
    var $file_tags;
    var $file_type;

    function __construct()
    {
        parent::__construct();
        $this->dao = new FileDAO();
        $this->file_user_id = 0;
        $this->file_name = '';
        $this->file_size = 0;
        $this->file_visible = 1;
        $this->file_status_id = 1;
        $this->file_culture_id = 0;
        $this->file_parent_file_id = 0;
        $this->file_file_type_id = 1;
        $this->file_tags = '';
        $this->file_type = new FileType;
        $this->create_success_message = 'File successfully saved.';
        $this->update_success_message = 'File successfully updated.';
        $this->create_failure_message = 'File failed to be saved.';
        $this->update_failure_message = 'File failed to be updated';
    }
    function setFieldsFromArray($array)
    {
        parent::setFieldsFromArray($array);
        $this->file_type->setFieldsFromArray($array);
    }
    function getValidationMessages()
    {
        $messages = array();
        return $messages;
    }

    public function isUploadTooLarge($getPost, $getFiles, $getServerValue)
    {
    }

    public function watch($cms)
    {
    }

    public function view()
    {
    }

    public function canDownload($user)
    {
    }

    public function download()
    {
    }


    public function uploadFile($uploaded_file)
    {
    }

    public function uploadFiles($getFiles, $getPost)
    {
    }

}