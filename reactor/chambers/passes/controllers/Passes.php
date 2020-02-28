<?php

if (!defined('RAPPVERSION'))
    exit('No direct script access allowed');
/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      Passes
 * @filesource  Passes.controllers
 */

class passes extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Passes_model');
        $this->ctrl_name="passes";
        $this->module_name="passes";
    }
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            $data['ictdata']=$this->ictdata;
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
        echo json_encode($this->Passes_model->_getdata());
    }
    public function create(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $status=$this->Passes_model->create($this->ictdata['site_settings']->passconfig);
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
        	$this->rview->gen('add',$data);
        }
        else
            $this->four_zero_one();
    }
    public function edit($id='') {
        $data['id']=$id;
        $data['ictdata']=$this->ictdata;
        $data['info']=$this->Passes_model->getinfo($id);
        $data['ctrl_name']=$this->ctrl_name;
        $this->rview->gen('edit',$data);
    }
    public function save($id) {
        $status=$this->Passes_model->edit($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function exists($value='',$id=''){
        if($this->input->is_ajax_request())
        {
            $check=$this->Passes_model->exists($value);
                if(count($check)>0){
                    if($id!=''){
                        if($check[0]['id_passes']==$id){
                            $result['content'] = "";
                            $result['status']=TRUE;
                        }else{
                            $result['content'] = "<strong style='color:red'>Already available in database</strong>";
                            $result['status']=FALSE;
                        }
                    }else{
                        $result['content'] = "<strong style='color:red'>Already available in database</strong>";
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