<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 2:22 PM
 */

namespace cms\core\dao;

use cms\core\utilities\Response;
use PDO;

class DAO
{
    private static $_instance;
    private $pdo;
    var $table;
    var $id_column;
    var $table_fields;
    var $model;
    var $hasOne;
    var $visible_field;
    var $site_id_field;
    function __construct()
    {
        $this->connect();
        $this->hasOne = array();
    }
    private function connect()
    {
        $this->pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USER, DATABASE_PASS,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    static function getConnection()
    {
        if (self::$_instance === NULL)
        {
            self::$_instance = new DAO();
        }
        return self::$_instance;
    }
    function getLastError()
    {
        var_dump($this::getConnection()->pdo);
    }
    function addHasOne($model_table, $model_id_column, $model_key)
    {
        array_push($this->hasOne, array('model_table'=>$model_table, 'model_id_column'=>$model_id_column, 'model_key'=>$model_key));
    }
    function __clone()
    {
        return false;
    }
    function __wakeup()
    {
        return false;
    }
    protected function runQuery($query, $params)
    {
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        return $pdo_statement->execute($params);
    }
    protected function execute($query, $params)
    {
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        $pdo_statement->execute($params);
        return $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
    }
    function getFiltered($filter = array(), $exclude_id = 0, $additional_query = '')
    {
        $query = "SELECT * FROM {$this->table}";
        foreach($this->hasOne AS $join)
        {
            $query = $this->join($query, $join['model_table'], $join['model_id_column'], $join['model_key']);
        }
        $params = array();
        $where = " WHERE";
        $param_counter = 1;
        foreach($filter AS $k=>$v)
        {
            if (is_array($v))
            {
                $query .= $where .= " {$this->table}.{$k} IN (";
                $comma = '';
                foreach($v AS $value)
                {
                    $query .= "{$comma}:{$k}_{$param_counter}";
                    $params[':'.$k . '_' . $param_counter] = $value;
                    $comma = ', ';
                    $param_counter++;
                }
                $query .= ')';
            }
            else
            {
                $query .= $where . " {$this->table}.{$k} = :{$k}";
                $params[':'.$k] = $v;
                $param_counter++;
            }
            $where = ' AND';
        }
        if ($exclude_id > 0)
        {
            $query .= " AND {$this->table}.{$this->id_column} != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        $query .= $additional_query;
        $results = $this->query($query, $params);
        return $results;
    }
    function join($query, $table, $id_column, $key)
    {
        $query .= " LEFT JOIN {$table} ON {$this->table}.{$key} = {$table}.{$id_column}";
        return $query;
    }
    function getAll()
    {
        $query = "SELECT * FROM {$this->table}";
        foreach($this->hasOne AS $join)
        {
            $query = $this->join($query, $join['model_table'], $join['model_id_column'], $join['model_key']);
        }
        if (strlen($this->visible_field) > 0)
            $query .= " WHERE {$this->table}.{$this->visible_field} = 1";
        return $this->execute($query, array());
    }
    function getAllBySiteId($site_id)
    {
        $query = "SELECT * FROM {$this->table}";
        foreach($this->hasOne AS $join)
        {
            $query = $this->join($query, $join['model_table'], $join['model_id_column'], $join['model_key']);
        }
        $query .= " WHERE {$this->table}.{$this->site_id_field} = ". $site_id;
        if (strlen($this->visible_field) > 0)
            $query .= " AND {$this->table}.{$this->visible_field} = 1";
        return $this->execute($query, array());
    }
    function query($query, $params)
    {
        return $this->execute($query, $params);
    }
    function queryOne($query, $params)
    {
        $results = $this->execute($query, $params);
        if (count($results) > 0)
            return $results[0];
        return array();
    }
    function create($model)
    {
        $id = $this->id_column;
        if ($model->$id > 0)
            return $this->update($model);
        $fields = $model->dao->table_fields;
        $query = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (";
        $params = array();
        $comma = '';
        foreach($fields AS $field)
        {
            $query .= "{$comma}:{$field}";
            $comma = ',';
            $params[$field] = $model->$field;
        }
        $query .= ")";
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        $response = new Response();
        try
        {
            $result = $pdo_statement->execute($params);
            $response->setStatus(true);
            $response->addMessage('successes', 'create', $model->create_success_message);
            $model->{$this->id_column} = $this::getConnection()->pdo->lastInsertId($this->id_column);
        }
        catch(\PDOException $e)
        {
            $response->addMessage('errors', 'create', array($e->getMessage(), $model->create_failure_message));
        }
        return $response;
    }
    function read($id)
    {
        $query = "SELECT * FROM {$this->table} ";
        foreach($this->hasOne AS $join)
        {
            $query = $this->join($query, $join['model_table'], $join['model_id_column'], $join['model_key']);
        }
        $query .= " WHERE {$this->table}.{$this->id_column} = :id";
        if (strlen($this->visible_field) > 0)
            $query .= " AND {$this->table}.{$this->visible_field} = 1";
        $params = array(':id'=>$id);
        return $this->queryOne($query, $params);
    }
    function update($model)
    {
        $fields = $model->dao->table_fields;
        $id_column = $model->dao->id_column;
        $query = "UPDATE {$this->table} SET ";
        $params = array();
        $comma = '';
        foreach($fields AS $field)
        {
            $query .= "{$comma}{$field}=:{$field}";
            $comma = ',';
            $params[$field] = $model->$field;
        }
        $query .= " WHERE {$this->table}.{$this->id_column} = :id";
        $params['id'] = $model->$id_column;
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        $response = new Response();
        try
        {
            $result = $pdo_statement->execute($params);
            $response->setStatus(true);
            $response->addMessage('successes', 'update', $model->update_success_message);
        }
        catch(\PDOException $e)
        {
            $response->addMessage('errors', 'update', array($e->getMessage(), $model->update_failure_message));
        }
        return $response;
    }
    function delete($model)
    {
        $field = $this->visible_field;
        $model->$field = 0;
        return $this->update($model);
    }
    function loseHistoryDelete($model)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->table}.{$this->id_column} = :id";
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        $id_column = $this->id_column;
        return $pdo_statement->execute(array(':id'=>$model->$id_column));
    }
    function loseHistoryDeleteByFilter($filter)
    {
        $query = "DELETE FROM {$this->table}";
        $params = array();
        $where = " WHERE";
        $param_counter = 1;
        foreach($filter AS $k=>$v)
        {
            if (is_array($v))
            {
                $query .= $where .= " {$this->table}.{$k} IN (";
                $comma = '';
                foreach($v AS $value)
                {
                    $query .= "{$comma}:{$k}_{$param_counter}";
                    $params[':'.$k . '_' . $param_counter] = $value;
                    $comma = ', ';
                    $param_counter++;
                }
                $query .= ')';
            }
            else
            {
                $query .= $where . " {$this->table}.{$k} = :{$k}";
                $params[':'.$k] = $v;
                $param_counter++;
            }
            $where = ' AND';
        }
        $pdo_statement = $this::getConnection()->pdo->prepare($query);
        $pdo_statement->execute($params);
    }
}