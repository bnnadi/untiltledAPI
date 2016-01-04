<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 3:02 PM
 */

namespace cms\core\dao;


class FileDAO extends DAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'files';
        $this->id_column = 'file_id';
        $this->model = '\bisikecms\core\model\File';
        $this->table_fields = array('file_name', 'file_display_name', 'file_size', 'file_created_date', 'file_modified_date', 'file_status_id', 'file_visible', 'file_mime_type', 'file_file_type_id','file_image_alt_tag','file_tags');
        $this->visible_field = 'file_visible';
        $this->addHasOne('files_types', 'file_type_id', 'file_file_type_id');
    }
    function getFiles($user, $page)
    {
        $query = "	SELECT * FROM folders_files
					RIGHT JOIN files ON folders_files.folder_file_file_id = files.file_id
					WHERE folders_files.folder_file_folder_id = :folder_id ORDER BY folder_file_sort ASC";
        $params = array(':folder_id'=>$page->page_folder_id);
        return $this::getConnection()->query($query, $params);
    }
}