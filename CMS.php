<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:14 PM
 */

namespace cms;


class CMS
{
    var $server;
    var $get;
    var $post;
    var $files;
    var $culture;
    var $culture_routing;

    function initialize()
    {
        date_default_timezone_set('UTC');
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        if (empty($this->post)) {
            $this->post = json_decode(file_get_contents ('php://input'), true);
        }
        $this->files = $_FILES;
        $this->culture_routing = false;
        spl_autoload_register(function($class_name){
            $file_name = str_replace('\\', '/', $class_name);
            if (file_exists(SERVER_ROOT . $file_name . '.php'))
            {
                include_once(SERVER_ROOT . $file_name . '.php');
            }
            elseif(file_exists(CMS_ROOT . $file_name . '.php'))
            {
                include_once(CMS_ROOT . $file_name . '.php');
            }
            else
            {
                throw new \Exception("There was an error finding this class name: {$class_name}");
            }
        });
        if (DEBUG)
        {
            error_reporting (E_ALL); // Development Level (development)
        }
        set_error_handler(array($this, 'cms_error'));
    }
    function cms_error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $message = 	"A PHP script error occurred on the ";
        $message .=	SITE_URL . " website.<br/>";
        $message .=	"The error occurred in file '$errfile'<br/>";
        $message .=	"on line $errline: $errstr<br/><br/>";
        $message .= "The page being viewed was: ". $_SERVER['REQUEST_URI'].'<br/>';
        if (isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) > 0) {
            $message .= "The referring page was: " . $_SERVER['HTTP_REFERER'] . '<br/>';
        }
        if (isset($_SESSION['REMOTE_ADDR']) && strlen($_SESSION['REMOTE_ADDR']) > 0) {
            $message .= "The IP Address was: ". $_SESSION['REMOTE_ADDR'] . '<br/>';
        }
        $message .= "The errno was : " . $errno;
        if (DEBUG)
        {
            echo '<div data-alert class="alert-box alet">' . $message . '</div>';
        }
        else
        {
            error_log ($message, 1, ADMIN_EMAIL);
            switch ($errno) {
                case E_NOTICE:
                case E_USER_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                case E_STRICT:
                    break;
                case E_WARNING:
                case E_USER_WARNING:
                    break;
                case E_ERROR:
                case E_USER_ERROR:
                    $warning = "An error occurred. PLEASE NOTE: A message containing the nature of this problem has been sent to the admins of " . SITE_URL . ".";
                    echo '<div data-alert class="alert-box alet">' . $warning . '</div>';
                    break;
                default:
                    break;
            }
        }
    }
    function getIncludePath($path, $file_name)
    {
        if (file_exists(SITE_INCLUDES . $path . $file_name))
        {
            return SITE_INCLUDES . $path . $file_name;
        }
        elseif (file_exists(CMS_INCLUDES . $path . $file_name))
        {
            return CMS_INCLUDES . $path . $file_name;
        }
        return '';
    }
    function setPrettyGetValue($key, $value)
    {
        $this->get[$key] = $value;
    }
    function setCultureRouting($routing)
    {
        $this->culture_routing = $routing;
    }
    function getCultureRouting()
    {
        return $this->culture_routing;
    }
    function setCulture($culture)
    {
        $this->culture = $culture;
    }
    function getCultureCode()
    {
        return $this->culture['culture_code'];
    }
    function getCultureURLPrefix()
    {
        if ($this->getCultureRouting())
            return $this->culture['culture_url_prefix'];
        return '';
    }
    function getCultureSiteURL()
    {
        $culture_prefix = $this->getCultureURLPrefix();
        if (strlen($culture_prefix) > 0)
            return SITE_URL . '/' . $culture_prefix;
        return SITE_URL;
    }
    function getRequestController($service)
    {
        $controller_file = "\\cms\\core\\controller\\{$service}Controller";
        try
        {
            $controller = new $controller_file();
            return $controller;
        }
        catch(\Exception $e)
        {
        }
        $controller_file = "\\cms\\extensions\\controller\\{$service}Controller";
        try
        {
            $controller = new $controller_file();
            return $controller;
        }
        catch(\Exception $e)
        {
        }
        return false;
    }
    function getPost()
    {
        return $this->post;
    }
    function getGet()
    {
        return $this->get;
    }
    function getFiles()
    {
        return $this->files;
    }
    function getServer()
    {
        return $this->server;
    }
    function getPostValue($field)
    {
        if ($this->isPost() && isset($this->post[$field]))
            return $this->post[$field];
        return '';
    }
    function getGetValue($field)
    {
        if (isset($this->get[$field]))
            return $this->get[$field];
        return '';
    }
    function shouldTrack()
    {
        if ($this->server['HTTP_DNT'] === 0)
            return true;
        return false;
    }
    function getRequestIP()
    {
        return $this->server['REMOTE_ADDR'];
    }
    function getRequestTime()
    {
        return $this->server['REQUEST_TIME_FLOAT'];
    }
    function isPost()
    {
        if ($this->server['REQUEST_METHOD'] == 'POST')
            return true;
        return false;
    }
    function isHTTPS()
    {
        if (isset($this->server['HTTPS']) && $this->server['HTTPS'] == 'on')
            return true;
        return false;
    }
    function getRequestURI()
    {
        return $this->server['REQUEST_URI'];
    }
    function getRequestFile()
    {
        $uri = $this->getRequestURI();
        return strtok($uri, '?');
    }
    function getServerValue($field)
    {
        if (isset($this->server[$field]))
            return $this->server[$field];
        return '';
    }
    function canUserAccessPage($user, $page)
    {
        $role = new \cms\core\model\Role();
        $role->role_id = $user->user_role_id;
        if ($role->isRoleInRoleGroup($page['page_role_group_id']))
            return true;
        return false;
    }
    // use this to replace constant strings from the cms for the string you're passing in
    // ie page_body in cms is saved as ::SITE_URL, gets replaced with www.website.com, etc
    function injectConstants($string)
    {
        $array = array
        (
            '::SITE_URL'=>SITE_URL
        ,'::CULTURE_SITE_URL'=>$this->getCultureSiteURL()
        ,'::API_URL'=>API_URL
        ,'::CMS_URL'=>CMS_URL
        );
        foreach($array AS $k=>$v)
        {
            $string = str_replace($k, $v, $string);
        }
        return $string;
    }
    function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }
}