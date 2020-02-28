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
 * @module      Reportinspector
 * @filesource  Reportinspector.controllers
 */

class Reportinspector extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Reportinspector_model');
        $this->ctrl_name="reportinspector";
        $this->module_name="inspector-report";
    }
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            
            if($this->rauth->is_depot()):
                $data['depot']=$this->rauth->login_depot();
            else:
                $data['depot']=NULL;
                $data['depots']=$this->Reportinspector_model->_getdepots();
            endif;
            $data['inspector']=$this->Reportinspector_model->_getinspectors();
            $data['buses']=$this->Reportinspector_model->_getbuses();
            $data['routes']=$this->Reportinspector_model->_getroutes();
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
    public function ajax_response(){
        $range = $this->rinput->post('range');
        $depot = $this->rinput->post('depot');
        $inspector = $this->rinput->post('inspector');
        $bus = $this->rinput->post('bus');
        $route = $this->rinput->post('route');
        if($depot=='all')
            $depot=NULL;
        if($inspector=='all')
            $inspector=NULL;
        if($bus=='all')
            $bus=NULL;
        if($route=='all')
            $route=NULL;
        $data=array();
        $data['depot']      = $depot;
        $date=explode('-', $range);
        $data['results']      = $this->Reportinspector_model->_getdata($date,$depot,$inspector,$bus,$route);
        $data['records']      = array();
        $data['ictdata']=$this->ictdata;
        if($this->rauth->is_depot()):
            $data['depotck']=1;
        else:
            $data['depotck']=NULL;
        endif;
        $content=$this->rview->gen('response',$data,true);
        $response['status']=TRUE;
        $response['content']=$content;
        echo json_encode($response);
    }

}