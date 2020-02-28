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
 * @module      Routes
 * @filesource  Routes.controllers
 */

class Routes extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Routes_model');
        $this->ctrl_name="routes";
        $this->module_name="routes";
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
        echo json_encode($this->Routes_model->_getdata());
    }
    public function create(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $status=$this->Routes_model->create();
            echo json_encode($this->rview->astatus($status,TRUE));
        }
        else
            $this->four_zero_one();
    }
    public function modeladdnew(){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['ictdata']=$this->ictdata;
            $data['ctrl_name']=$this->ctrl_name;
            $this->rview->gen('add',$data);
        }
        else
            $this->four_zero_one();
    }
    public function faretablemode($id=''){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['id']=$id;
            $data['ictdata']=$this->ictdata;
            $data['ctrl_name']=$this->ctrl_name;
            $data['info']=$this->Routes_model->getinfo($id);
            $this->rview->gen('faretablemode',$data);
        }
        else
            $this->four_zero_one();
    }
    public function faretable($id=''){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $data['ctrl_name']=$this->ctrl_name;
            $data['id']=$id;
            $data['ictdata']=$this->ictdata;
            $data['info']=$this->Routes_model->getinfo($id);
            $response['status']=TRUE;
            $mode=$this->rinput->post('f_table_mode');
            if($mode=='1'){//CSV
                $response['content']=$this->rview->gen('addfaretablecsv',$data,TRUE);
            }
             if($mode=='2'){
                $response['content']=$this->rview->genajax('addfaretableentry',$data);
             }
        echo json_encode($response);
        }
        else
            $this->four_zero_one();
    }
    public function faretablesave($id=''){
        if($this->rauth->has_accesslevel($this->module_name,'create'))
        {
            $status=$this->Routes_model->faretable($id);
            echo json_encode($this->rview->astatus($status,TRUE));
        }
        else
            $this->four_zero_one();
    }
   function faretable_ajaximport($id=''){
        if($this->input->is_ajax_request()){
            echo json_encode($this->rview->astatus($this->Routes_model->faretablecsv($id),TRUE));
        }else{
            $this->load->view('401');
        }
   }
    function faretable_csvexport($id=''){
        $response=$this->Routes_model->faretablecsvdata($id);
        $routename=$response['routename'];
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"$routename".".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        $handle = fopen('php://output', 'w');
        foreach ($response['data'] as $data) {
            fputcsv($handle, $data);
        }
            fclose($handle);
        exit;
    }
    public function edit($id='') {
        $data['id']=$id;
        $data['ictdata']=$this->ictdata;
        $data['info']=$this->Routes_model->getinfo($id);
        $data['ctrl_name']=$this->ctrl_name;
        $this->rview->gen('edit',$data);
    }
    public function save($id) {
        $status=$this->Routes_model->edit($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function exists($value='',$id=''){
        if($this->input->is_ajax_request())
        {
            $check=$this->Routes_model->exists($value);
                if(count($check)>0){
                    if($id!=''){
                        if($check[0]['id_routes']==$id){
                            $result['content'] = "";
                            $result['status']=TRUE;
                        }else{
                            $result['content'] = "<strong style='color:red'>Already route id in use</strong>";
                            $result['status']=FALSE;
                        }
                    }else{
                        $result['content'] = "<strong style='color:red'>Already route id in use</strong>";
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