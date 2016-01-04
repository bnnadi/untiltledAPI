<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:31 PM
 */

namespace cms\core\model;


use cms\core\dao\SiteDAO;
use cms\core\utilities\Security;

class Site extends Model
{
    var $site_id;
    var $site_name;
    var $site_url;
    var $site_main_navigation_group_id;
    var $site_footer_navigation_group_id;
    var $site_created_date;
    var $site_modified_date;
    var $site_status_id;
    var $site_visible;
    var $site_analytics_email;
    var $site_analytics_password;
    var $site_hosting_domain;
    var $site_hosting_email;
    var $site_hosting_password;
    function __construct()
    {
        $this->dao = new SiteDAO();
    }
    function getAllSites()
    {
        return $this->dao->getAllSites();
    }
    function getAllFrontendSites()
    {
        return $this->dao->getAllFrontendSites();
    }
    function getValidationMessages()
    {
        return array();
    }
    function encrpytPassword($password)
    {
        $security = new Security;
        return $security->encrypt($password);
    }
    function decrpytPassword($password)
    {
        $security = new Security;
        return $security->decrypt($password);
    }
    function getSocialLinks(){
        $social = new \cms\core\dao\SocialLinkDAO();
        $socialArr = array(
            'social_link_site_id' => $this->site_id,
            'social_link_visible' => 1
        );
        return $social->getFiltered($socialArr, 0, '');
    }
    function searchSite($site_id,$term,$start,$per_page){
        $pages = new \cms\core\dao\PageDAO();
        return array($pages->searchFrontendPages($site_id,$term,$start,$per_page),$pages->getSearchFrontendPagesCount($site_id,$term));
    }
}