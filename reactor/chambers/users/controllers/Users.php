<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      Users
 * @filesource  users.controller
 */

class Users extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Users_model');
        $this->ctrl_name="users";
        $this->module_name="users";
    }
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            $data['ictdata']=$this->ictdata;
            $data['page_name']="Users";
            $data['ctrl_name']=$this->ctrl_name;
            $render['subview']=$this->rview->gen('view',$data,TRUE);
            $render['ictdata']=$this->ictdata;
            $this->rview->genview($render);
        }
        else {
            $this->four_zero_one();
        }
    }
    public function ajax_list(){
        echo json_encode($this->Users_model->_getdata());
    }
    public function create(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $status=$this->Users_model->create();
            echo json_encode($this->rview->astatus($status,TRUE));
        }
        else
            $this->four_zero_one();
    }
    public function modeladdnew(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['ctrl_name']=$this->ctrl_name;
            $data['ictdata']=$this->ictdata;
            $data['depots']=$this->Users_model->getdepots();
            $this->rview->gen('add',$data);
        }
        else
            $this->four_zero_one();
    }
    public function modalpass($id=''){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['ctrl_name']=$this->ctrl_name;
            $data['id']=$id;
            $data['ictdata']=$this->ictdata;
            $this->rview->gen('password',$data);
        }
        else
            $this->four_zero_one();
    }
    public function setpassword($id) {
        $status=$this->Users_model->passwordupdate($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function modalpermission($id=''){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['id']=$id;
            $data['ictdata']=$this->ictdata;
            $data['ctrl_name']=$this->ctrl_name;
            $data['info']=$this->Users_model->getinfo($id);
            $data['permitsinfo']=$this->Users_model->getpermission($id);
            $this->rview->gen('permission',$data);
        }
        else
            $this->four_zero_one();
    }
    public function permissionsave($id) {
        $status=$this->Users_model->permissionupdate($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function edit($id='') {
        $data['id']=$id;
        $data['ictdata']=$this->ictdata;
        $data['info']=$this->Users_model->getinfo($id);
        $data['depots']=$this->Users_model->getdepots();
        $data['ctrl_name']=$this->ctrl_name;
        $this->rview->gen('edit',$data);
    }
    public function save($id) {
        $status=$this->Users_model->edit($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function exists($value='',$id=''){
        if($this->input->is_ajax_request())
        {
            $check=$this->Users_model->exists($value);
                if(count($check)>0){
                    if($id!=''){
                        if($check[0]['id_depots']==$id){
                            $result['content'] = "";
                            $result['status']=TRUE;
                        }else{
                            $result['content'] = "<strong style='color:red'>User Name already registered</strong>";
                            $result['status']=FALSE;
                        }
                    }else{
                        $result['content'] = "<strong style='color:red'>User Name already registered</strong>";
                        $result['status']=FALSE;
                    }
                }else{
                    
                    $result['content'] = "";
                    $result['status']=TRUE;
                }
                echo json_encode($result);
            exit;
        }else {
                $this->four_zero_one();
        }
    }
}