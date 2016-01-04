<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:54 PM
 */

namespace cms\core\dao;


class SiteDAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'sites';
        $this->id_column = 'site_id';
        $this->model = '\bisikecms\core\model\Site';
        $this->table_fields = array('site_name', 'site_url', 'site_created_date', 'site_modified_date', 'site_status_id', 'site_visible');
        $this->visible_field = 'site_visible';
    }
    function getAllFrontendSites()
    {
        return $this->getAllSites();
    }
    function getAllSites()
    {
        $query = "SELECT * FROM {$this->table} WHERE {$this->visible_field} = 1";
        $params = array();
        return $this::getConnection()->query($query, $params);
    }
}