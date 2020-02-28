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
 * @module      Configtool
 * @filesource  Configtool.controllers
 */
 
class Configtool extends BE_Controller {

	function __construct() {
        parent::__construct();
        $this->load->model('Configtool_model');
        $this->ctrl_name="configtool";
        $this->module_name="config-tool";
    }
    
    public function index() {   
        if($this->rauth->has_access($this->module_name))
        {
            $data['ictdata']=$this->ictdata;
            $data['ctrl_name']=$this->ctrl_name;
            $data['devices']=array();
            if($this->rauth->is_depot()):
                $data['depotsid']=$this->rauth->login_depot();
                $depotinfo=$this->Configtool_model->_fetch_depotbyid($data['depotsid']);
                if($depotinfo->config_status==1&&$this->ictdata['site_settings']->passconfig==1){
	                $data['devices']=$this->Configtool_model->_fetch_devicesbydepots($data['depotsid']);
	            	$render['subview']=$this->rview->gen('view',$data,TRUE);
                }else{
	            	$render['subview']=$this->rview->gen('warning',$data,TRUE);
                }
            else:
                if($this->ictdata['site_settings']->passconfig==1){
                	$data['depots']=$this->Configtool_model->_fetch_depot();
            		$render['subview']=$this->rview->gen('view',$data,TRUE);
                }else{
	            	$render['subview']=$this->rview->gen('warning',$data,TRUE);
                }
            endif;
            $render['ictdata']=$this->ictdata;
            $this->rview->genview($render);
        }
        else {
            $this->four_zero_one();
        }
    }
    public function configure(){
        if($this->input->is_ajax_request()){
            $status=$this->Configtool_model->configure();
            echo json_encode($status);
        }else
            $this->four_zero_one();
        
    }
    public function fetchdevice() {   
        if($this->input->is_ajax_request()){
            $id=$this->input->post('depot');
            $devices=$this->Configtool_model->_fetch_devicesbydepots($id);
            $html='<option></option>';
            foreach($devices as $device)
                $html.='<option value="'.$device['id_devices'].'">'.$device['uid'].'</option>';
            echo $html;
        }
        else
            $this->four_zero_one();
    }
    public function fetchconfig() {   
        if($this->input->is_ajax_request()){
            $id=$this->input->post('device');
            $data=$this->Configtool_model->_fetch_data($id);
            $html='<option></option>';
            foreach($data['drivers'] as $row):
                if($data['isconfig']&&$data['driverid']==$row['id_drivers'])
                    $html.='<option value="'.$row['id_drivers'].'" selected>'.$row['driver_name'].$row['driver_id'].'</option>';
                else
                    $html.='<option value="'.$row['id_drivers'].'">'.$row['driver_name'].$row['driver_id'].'</option>';
            endforeach;
            $resonse['driver']=$html;
            $html='<option></option>';
            foreach($data['conductors'] as $row):
                if($data['isconfig']&&$data['conductorid']==$row['id_conductors'])
                    $html.='<option value="'.$row['id_conductors'].'" selected>'.$row['conductor_name'].$row['conductor_id'].'</option>';
                else
                    $html.='<option value="'.$row['id_conductors'].'">'.$row['conductor_name'].$row['conductor_id'].'</option>';
                
            endforeach;
            $resonse['conductor']=$html;
            $html='<option></option>';
            foreach($data['buses'] as $row):
                if($data['isconfig']&&$data['busid']==$row['id_buses'])
                    $html.='<option value="'.$row['id_buses'].'" selected>'.$row['bus_no'].'</option>';
                else
                    $html.='<option value="'.$row['id_buses'].'">'.$row['bus_no'].'</option>';
                
            endforeach;
            $resonse['bus']=$html;
            $html='<option></option>';
            foreach($data['routes'] as $row):
                if(!is_array($data['routeid']))
                    $html.='<option value="'.$row['id_routes'].'">'.$row['route_no'].' - '.$row['route_name'].'</option>';
                elseif($data['isconfig']&&in_array($row['id_routes'], $data['routeid']))
                    $html.='<option value="'.$row['id_routes'].'" selected>'.$row['route_no'].' - '.$row['route_name'].'</option>';
                else
                    $html.='<option value="'.$row['id_routes'].'">'.$row['route_no'].' - '.$row['route_name'].'</option>';
            endforeach;
            $resonse['route']=$html;
            echo json_encode($resonse);
        }
        else
            $this->four_zero_one();
    }
}
