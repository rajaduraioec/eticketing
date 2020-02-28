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
 * @module      Headerfooter
 * @filesource  Headerfooter.controllers
 */
 
class Headerfooter extends BE_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('Headerfooter_model');
        $this->ctrl_name="headerfooter";
        $this->module_name="header-footer";
    }
    
    public function index(){
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
        echo json_encode($this->Headerfooter_model->_getdata());
    }
    
    public function create(){
    	if($this->rauth->has_accesslevel($this->module_name,'create')){
    		$status=$this->Headerfooter_model->create();
    		echo json_encode($this->rview->astatus($status,TRUE));
    	} else {
    		$this->four_zero_one();
    	}
    }
    
    public function modeladdnew(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['ctrl_name']=$this->ctrl_name;
            $data['ictdata']=$this->ictdata;
            $data['depots']=$this->Headerfooter_model->get_depots();
        	$this->rview->gen('add',$data);
        }
        else
            $this->four_zero_one();
    }
    
    public function edit($id='') {
        $data['id']=$id;
        $data['ictdata']=$this->ictdata;
        $data['info']=$this->Headerfooter_model->getinfo($id);
        $data['ctrl_name']=$this->ctrl_name;
        $this->rview->gen('edit',$data);
    }
    
    public function save($id){
    	$status=$this->Headerfooter_model->edit($id);
		echo json_encode($this->rview->astatus($status,TRUE));
    }
    
}

?>