<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:22 PM
 */

namespace cms\core\Model;


use cms\core\dao\DAO;
use cms\core\utilities\Response;

class Model
{
    var $dao;
    var $create_success_message;
    var $update_success_message;
    var $create_failure_message;
    var $update_failure_message;
    function __construct()
    {
        $this->dao = new DAO();
        $this->create_success_message = 'Record successfully saved.';
        $this->update_success_message = 'Record succesfully updated.';
        $this->create_failure_message = 'Record failed to be saved.';
        $this->update_failure_message = 'Record failed to be updated';
    }
    function setFieldsFromModel($model)
    {
        $fields = $this->dao->table_fields;
        $id = $this->dao->id_column;
        if (property_exists($this, $id) && isset($model->$id))
            $this->$id = $model->$id;
        foreach($fields AS $field)
        {
            if (property_exists($this, $field) && isset($model->$field))
                $this->$field = $model->$field;
        }
    }
    function setFieldsFromArray($array)
    {
        $fields = get_object_vars($this);
        $id = $this->dao->id_column;
        if (property_exists($this, $id) && isset($array[$id]))
            $this->$id = $array[$id];
        foreach($fields AS $k=>$v)
        {
            if (is_string($k))
            {
                if (property_exists($this, $k) && isset($array[$k]))
                    $this->$k = $array[$k];
            }
        }
    }
    abstract function getValidationMessages();
    function getAll()
    {
        return $this->dao->getAll();
    }
    function getAllBySiteId($site_id)
    {
        return $this->dao->getAllBySiteId($site_id);
    }
    function getFiltered($filter = array(), $exclude_id = 0, $additional_query = '')
    {
        return $this->dao->getFiltered($filter, $exclude_id, $additional_query);
    }
    function hasDuplicate($filter, $exclude_id)
    {
        $results = $this->dao->getFiltered($filter, $exclude_id);
        if (count($results) > 0)
            return true;
        return false;
    }
    function getAllLocalizedMeta($record_id, $culture_code)
    {
        $local_meta = $this->dao->getAllLocalizedMeta($record_id, $culture_code);
        $meta = array();
        foreach($local_meta AS $this_meta)
        {
            $meta_thing = new $this->dao->model;
            $meta_thing->setFieldsFromArray($this_meta);
            array_push($meta, $meta_thing);
        }
        return $meta;
    }
    function create()
    {
        $messages = $this->getValidationMessages();
        if (count($messages) > 0)
        {
            $response = new Response();
            $i = 0;
            foreach($messages AS $k=>$v)
            {
                $response->addMessage('errors', $k, $v);
            }
            return $response;
        }
        return $this->dao->create($this);
    }
    function read($id)
    {
        $read_array = $this->dao->read($id);
        if ($read_array)
        {
            $this->setFieldsFromArray($read_array);
        }
        return $read_array;
    }
    function update()
    {
        $messages = $this->getValidationMessages();
        if (count($messages) > 0)
        {
            $response = new Response();
            $i = 0;
            foreach($messages AS $k=>$v)
            {
                $response->addMessage('errors', $k, $v);
            }
            return $response;
        }
        return $this->dao->update($this);
    }
    function delete($model)
    {
        return $this->dao->delete($model);
    }
    function loseHistoryDelete($model)
    {
        return $this->dao->loseHistoryDelete($model);
    }
    function loseHistoryDeleteByFilter($filter)
    {
        return $this->dao->loseHistoryDeleteByFilter($filter);
    }
}