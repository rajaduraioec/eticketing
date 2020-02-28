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
 * @module      Reportbreakdown
 * @filesource  Reportbreakdown.controllers
 */

class Reportbreakdown extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Reportbreakdown_model');
        $this->ctrl_name="reportbreakdown";
        $this->module_name="breakdown-report";
    }
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            
            if($this->rauth->is_depot()):
                $data['depot']=$this->rauth->login_depot();
            else:
                $data['depot']=NULL;
                $data['depots']=$this->Reportbreakdown_model->_getdepots();
            endif;
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
        if($depot=='all')
            $depot=NULL;
        $data=array();
        $data['depot']      = $depot;
        $data['ictdata']=$this->ictdata;
        $date=explode('-', $range);
        $data['results']      = $this->Reportbreakdown_model->_getdata($date,$depot);
        $data['records']      = array();
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