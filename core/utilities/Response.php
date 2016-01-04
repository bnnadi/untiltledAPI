<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:49 PM
 */

namespace cms\core\utilities;


class Response
{
    var $status;
    var $messages;
    var $data;
    function __construct()
    {
        $this->status = 0;
        $this->messages = array();
        $this->data = array();
    }
    function addMessage($message_group, $key, $message)
    {
        $this->messages[$message_group][$key] = $message;
    }
    function addValidationMessages($messages)
    {
        array_merge($this->messages['errors'], $messages);
    }
    function addData($key, $message)
    {
        $this->data[$key] = $message;
    }
    function setStatus($status)
    {
        $this->status = (int)$status;
    }
    function getMessages()
    {
        return $this->messages;
    }
    function getData()
    {
        return $this->data;
    }
    function getDataByKey($key)
    {
        if (isset($this->data[$key]))
            return $this->data[$key];
        return null;
    }
    function getMessageByKey($message_group, $key)
    {
        if (isset($this->messages[$message_group][$key]))
            return $this->messages[$message_group][$key];
        return null;
    }
    function getStatus()
    {
        return $this->status;
    }
    function merge($other_response)
    {
        $this->status = $this->status & $other_response->getStatus();
        $this->messages = array_merge($this->messages, $other_response->getMessages());
        $this->data = array_merge($this->data, $other_response->getData());
    }
}