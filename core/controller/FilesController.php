<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 3:52 PM
 */

namespace cms\core\controller;


use cms\core\model\File;

class FilesController extends Controller
{
    const FILES_PERMISSION = 4;
    function index($parameter = 0)
    {
        if ($this->cms->isPost())
        {
            $file = new File();
            if ($file->isUploadTooLarge($this->cms->getPost(), $this->cms->getFiles(), $this->cms->getServerValue('CONTENT_LENGTH')))
            {
                $this->response->addMessage('errors', 'file', 'Upload was too large.');
            }
            else
            {
                if (true)
                {
                    $file->file_user_id = (int)$this->user->user_id;
                    if ($parameter == 0)
                    {
                        $file->setFieldsFromArray($this->cms->getPost());
                        $this->response = $file->uploadFiles($this->cms->getFiles(), $this->cms->getPost());
                        if ($this->response->getStatus())
                        {
                            $this->response->addData('file_id', $file->file_id);
                        }
                    }
                    else
                    {
                        $file->read($parameter);
                        $files = $this->cms->getFiles();
                        $file->setFieldsFromArray($this->cms->getPost());
                        if (count($files) > 0)
                        {
                            $uploaded_file = reset($files);
                            $this->response = $file->uploadFile($uploaded_file);
                        }
                        else
                        {
                            $this->response = $file->update();
                        }
                    }
                }
                else
                {
                    $this->response->addMessage('errors', 'auth', 'You are not authorized to perform this action.');
                }
            }
        }
        else
        {
            $file = new File();
            $files = array();
            if ($parameter > 0)
            {
                $read_file = $file->read($parameter);
                if (!empty($read_file))
                    $files = array($read_file);
            }
            else
            {
                $files = $file->getAll();
            }
            $data = array();
            foreach($files AS $this_file)
            {
                $ext = $this_file['file_name'];
                $extExploded = explode('.', $ext);
                if (count($extExploded) > 1)
                    $ext = $extExploded[(count($extExploded) - 1)];
                $data_array = array(
                    'id'=>$this_file['file_id'],
                    'type'=>$this_file['file_type_name'],
                    'file_display_name'=>$this_file['file_display_name'],
                    'name'=>$this_file['file_name'],
                    'size'=>$this_file['file_size'],
                    'file_image_alt_tag'=>$this_file['file_image_alt_tag'],
                    'ext'=>$ext,
                    'file_tags'=>$this_file['file_tags'],
                    'date'=>$this_file['file_modified_date']
                );
                array_push($data, $data_array);
            }
            $this->response->addData('files', $data);
            $this->response->setStatus(true);
        }
    }
    function download($parameter = 0)
    {
        $file = new File();

        if(!is_numeric($parameter)){
            $f = $file->getFiltered(array('file_name'=>$parameter), 0, '');
            foreach($f as $o){
                $file->read($o['file_id']);
            }
        } else {
            $file->read($parameter);
        }
        if ($file->file_id > 0)
        {
            if ($file->canDownload($this->user))
            {
                $file->download();
            }
            else
            {
                $this->response->addMessage('errors', 'auth', 'You do not have permissions to download this file.');
            }
        }
        else
        {
            die('no file');
        }
    }
    function view($parameter = 0)
    {
        $file = new File();
        if(!is_numeric($parameter)){
            $f = $file->getFiltered(array('file_name'=>$parameter), 0, '');
            foreach($f as $o){
                $file->read($o['file_id']);
            }
        } else {
            $file->read($parameter);
        }
        if ($file->file_id > 0)
        {
            $file->view();
        }
        else
        {
            die('no file');
        }
    }
    function watch($parameter = 0)
    {
        $file = new File();
        if(!is_numeric($parameter)){
            $f = $file->getFiltered(array('file_name'=>$parameter), 0, '');
            foreach($f as $o){
                $file->read($o['file_id']);
            }
        } else {
            $file->read($parameter);
        }
        if ($file->file_id > 0)
        {
            $file->watch($this->cms);
        }
        else
        {
            die('no file');
        }
    }
}