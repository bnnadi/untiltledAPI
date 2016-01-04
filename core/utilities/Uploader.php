<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:58 PM
 */

namespace cms\core\utilities;


class Uploader
{
    var $upload_path;
    var $file_name;
    var $error_message;
    function __construct()
    {
    }
    function setUploadPath($path)
    {
        $this->upload_path = $path;
    }
    function setFileName($filename)
    {
        $this->file_name = $filename;
    }
    function getErrorMessage()
    {
        return $this->error_message;
    }
    function uploadFile($file)
    {
        if ($file['error'] > 0)
        {
            switch($file['error'])
            {
                case UPLOAD_ERR_INI_SIZE:
                    $this->error_message = 'The uploaded file exceeded the server\'s maximum size limit.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->error_message = 'The uploaded file exceeded the maximum size limit.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->error_message = 'The file failed to fully upload.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->error_message = 'There was no file uploaded.';
                    break;
                default:
                    $this->error_message = 'There was an error uploading your file.';
                    break;
            }
            return false;
        }
        if (!file_exists($this->upload_path))
        {
            if (!mkdir($this->upload_path))
            {
                $this->error_message = 'The server had an error saving the file to the server. Please try again later.';
                return false;
            }
        }
        if (!move_uploaded_file($file['tmp_name'], $this->upload_path . $this->file_name))
        {
            $this->error_message = 'There was an error moving your file into the directory.';
            return false;
        }
        return true;
    }
}