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
 * @module      Tracking
 * @filesource  Tracking.controllers
 */

class Tracking extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Tracking_model');
        $this->ctrl_name="tracking";
        $this->module_name="tracking";
    }
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            if($this->rauth->is_depot()):
                $data['depot']=$this->rauth->login_depot();
            else:
                $data['depot']=NULL;
                $data['depots']=$this->Tracking_model->_getdepots();
            endif;
            $data['buses']=$this->Tracking_model->_getbuses();
            $data['ictdata']=$this->ictdata;
            $data['ctrl_name']=$this->ctrl_name;
            $render['subview']=$this->rview->gen('view',$data,TRUE);
            $render['ictdata']=$this->ictdata;
            $this->rview->genview($render);
        }else{
            $this->four_zero_one();
        }
    }
    public function ajax_response(){
        $depot = $this->rinput->post('depot');
        $bus = $this->rinput->post('buses');
        if($depot=='all')
            $depot=NULL;
        if($bus=='all')
            $bus=NULL;
        $data=array();
        $data['results']      = $this->Tracking_model->_getdata($depot,$bus);
        $data['ictdata']=$this->ictdata;
        $content=$this->rview->gen('response',$data,true);
        $response['status']=TRUE;
        $response['content']=$content;
        echo json_encode($response);
    }

}