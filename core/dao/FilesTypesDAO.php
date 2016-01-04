<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 3:02 PM
 */

namespace cms\core\dao;


class FilesTypesDAO
{
    function __construct()
    {
        parent::__construct();
        $this->model = '\bisikecms\core\model\FileTypes';
        $this->table = 'files_types';
        $this->id_column = 'file_type_id';
        $this->table_fields = array('file_type_id', 'file_type_name');
    }
}