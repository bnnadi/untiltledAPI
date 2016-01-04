<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:37 PM
 */

namespace cms\core\model;


use cms\core\dao\FilesTypesDAO;

class FileType extends Model
{
    var $file_type_id;
    var $file_type_name;
    function __construct()
    {
        $this->dao = new FilesTypesDAO();
    }
    function getValidationMessages()
    {
        return array();
    }
}