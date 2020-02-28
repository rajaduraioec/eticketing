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
 * @module      Dashboard
 * @filesource  Dashboard.Controllers
 */

class Dashboard extends BE_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->ctrl_name="dashboard";
    }
    public function index()
    {
        $data['page_name']="";
        $data['ctrl_name']=$this->ctrl_name;
        $data['ictdata']=$this->ictdata;
        if($this->rauth->is_depot())
            $render['subview']=$this->rview->gen('view-depot',$data,TRUE);
        else
            $render['subview']=$this->rview->gen('view-admin',$data,TRUE);
//        $render['subview']=$this->rview->gen('undercoding',array(),TRUE);
        $render['ictdata']=$this->ictdata;
        $this->rview->genview($render);
    }
    public function ajax_livestatus(){
        if($this->rauth->isajax()):
        $data['results']      = $this->Dashboard_model->_getlivedata();
        $data['colourtype']      = 1;
        $data['ictdata']=$this->ictdata;
        if($this->rauth->is_depot())
            $content=$this->rview->gen('response-depot',$data,true);
        else
            $content=$this->rview->gen('response-admin',$data,true);
        $response['status']=TRUE;
        $response['content']=$content;
        $response['timestamp']='Updated On '.date('Y-m-d H:i:s');
        echo json_encode($response);
        else:exit('No direct script access allowed');
        endif;
    }
    public function ajax_laststatus(){
        if($this->rauth->isajax()):
        $data['results']      = $this->Dashboard_model->_getlastdata();
        $data['colourtype']      = 0;
        $data['ictdata']=$this->ictdata;
        if($this->rauth->is_depot())
            $content=$this->rview->gen('response-depot',$data,true);
        else
            $content=$this->rview->gen('response-admin',$data,true);
        $response['status']=TRUE;
        $response['content']=$content;
        $response['timestamp']='Updated On '.date('Y-m-d H:i:s');
        echo json_encode($response);
        else:exit('No direct script access allowed');
        endif;
    }
    public function ajdepotstatus($id=''){
        if($this->rauth->isajax()):
        $data['data']      = $this->Dashboard_model->_getdepotdata($id);
        $data['ictdata']=$this->ictdata;
        $this->rview->gen('response-depotwise',$data);
        else:exit('No direct script access allowed');
        endif;
    }
}
