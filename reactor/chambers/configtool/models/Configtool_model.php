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
 * @module      Configtool_model
 * @filesource  Configtool_model.models
 */
 
class Configtool_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    function configure(){
        
            $routes = $this->rinput->post('route-id');
            if(count($routes)>0):
                
            $cdata['configid'] = $this->genconfigid();
	        if($this->rinput->post('luggage_status')):
	            $cdata['luggage_status'] = 1;
	            else:
	            $cdata['luggage_status'] = 0;
	        endif;
            $cdata['depots_id'] = $this->rinput->post('depot-id');
            $cdata['devices_id'] = $this->rinput->post('device-id');
            $cdata['conductors_id'] = $this->getconductorid($this->rinput->post('conductor-id'));
            $cdata['drivers_id'] = $this->getdriverid($this->rinput->post('driver-id'));
            $cdata['buses_id'] = $this->rinput->post('bus-id');
            $cdata['odometer'] = $this->rinput->post('odometer');
            $cdata['shift_no'] = $this->rinput->post('shiftno');
            $cdata['duty_no'] = $this->rinput->post('dutyno');
            $configid= $this->rdb->create('config','deviceconfig',$cdata);
            $this->maproutes($configid,$routes);
            $return['status']=TRUE;
            $return['message']="Configured Successfully !!!";
            else:
            $return['status']=FALSE;
            $return['message']="Something Went Wrong Try Again !!!";
                
            endif;
            return $return;
            
    }
    function maproutes($configid='',$routes=array()){
        $count=count($routes);
        $query=array();
        $data=array();
        for($i=0;$i<$count;$i++):
            unset($query);
            unset($data);
            $query[]=$this->rdb->qb('where','id_routes',$routes[$i]);
            $info=$this->rdb->fetch('default','routes',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('where','routes_id',$info->id_routes);
            $query[]=$this->rdb->qb('where','modifiedon',$info->modifiedon);
            $configinfo=$this->rdb->fetch('config','deviceroutes',$query);
            unset($query);
            if(count($configinfo)>0):
                $routeid= $configinfo['0']['id_deviceroutes'];
            else:
                $data['routes_id']=$info->id_routes;
                $data['route_name']=$info->route_name;
                $data['route_no']=$info->route_no;
                $data['service_type']=$info->service_type;
                $data['route_target']=$info->route_target;
                $data['load_factor']=$info->load_factor;
                $data['distance']=$info->distance;
                $data['fare_table_type']=$info->fare_table_type;
                $data['total_stages']=$info->total_stages;
                $data['stages']=$info->stages;
                $data['fare_table']=$info->fare_table;
                $data['modifiedon']=$info->modifiedon;
                $routeid= $this->rdb->create('config','deviceroutes',$data);
                unset($data);
            endif;    
            $data['deviceconfig_id']=$configid;
            $data['deviceroutes_id']=$routeid;
            $routeid= $this->rdb->create('config','deviceconfigroutes',$data);
            
        endfor;
        
    }
    function getconductorid($id=''){
        $query=array();
        $data=array();
        $query[]=$this->rdb->qb('where','id_conductors',$id);
        $info=$this->rdb->fetch('default','conductors',$query,FALSE,TRUE);
        unset($query);
        $query[]=$this->rdb->qb('where','conductors_id',$info->id_conductors);
        $query[]=$this->rdb->qb('where','modifiedon',$info->modifiedon);
        $configinfo=$this->rdb->fetch('config','deviceconductors',$query);
        unset($query);
        if(count($configinfo)>0):
            return $configinfo['0']['id_deviceconductors'];
        else:
            $data['conductors_id']=$info->id_conductors;
            $data['conductor_name']=$info->conductor_name;
            $data['conductor_id']=$info->conductor_id;
            $data['conductor_pass']=$info->conductor_pass;
            $data['modifiedon']=$info->modifiedon;
            $data['active']=$info->active;
            return $this->rdb->create('config','deviceconductors',$data);
        endif;
    }
    function getdriverid($id=''){
        $query=array();
        $data=array();
        $query[]=$this->rdb->qb('where','id_drivers',$id);
        $info=$this->rdb->fetch('default','drivers',$query,FALSE,TRUE);
        unset($query);
        $query[]=$this->rdb->qb('where','drivers_id',$info->id_drivers);
        $query[]=$this->rdb->qb('where','modifiedon',$info->modifiedon);
        $configinfo=$this->rdb->fetch('config','devicedrivers',$query);
        unset($query);
        if(count($configinfo)>0):
            return $configinfo['0']['id_devicedrivers'];
        else:
            $data['drivers_id']=$info->id_drivers;
            $data['driver_name']=$info->driver_name;
            $data['driver_id']=$info->driver_id;
            $data['modifiedon']=$info->modifiedon;
            $data['active']=$info->active;
            return $this->rdb->create('config','devicedrivers',$data);
        endif;
    }
    function genconfigid(){
        $time =microtime(true);
        $micro_time=sprintf("%06d",($time - floor($time)) * 1000000);
        $date=new DateTime( date('Y-m-d H:i:s.'.$micro_time,$time) );
        return $date->format("dyiHmus").rand(10, 99);
    }
    function _fetch_data($id=''){
        $query[]=$this->rdb->qb('where','devices_id',$id);
        $query[]=$this->rdb->qb('order_by','createdon','DESC');
        $query[]=$this->rdb->qb('limit','1');
        $config=$this->rdb->fetch('config','deviceconfig',$query);
        unset($query);
        if(count($config)>0){
            $response['isconfig']=TRUE;
            $query[]=$this->rdb->qb('where','id_deviceconductors',$config[0]['conductors_id']);
            $response['conductorid']=$this->rdb->fetch('config','deviceconductors',$query,FALSE,TRUE)->conductors_id;
            unset($query);
            $query[]=$this->rdb->qb('where','id_devicedrivers',$config[0]['drivers_id']);
            $response['driverid']=$this->rdb->fetch('config','devicedrivers',$query,FALSE,TRUE)->drivers_id;
            unset($query);
            $response['busid']= $config[0]['buses_id'];
            $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
            $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config[0]['id_deviceconfig']);
            $query[]=$this->rdb->qb('select','routes_id');
            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
            unset($query);
            $response['routeid']=array();
            foreach($routeinfo as $route)
                $response['routeid'][]=$route['routes_id'];
        }else{
            $response['isconfig']=FALSE;
            $response['driverid']=  '';
            $response['conductorid']=  '';
            $response['busid']=  '';
            $response['routeid']=  array();
        }
            $response['drivers']=  $this->_fetch_drivers();
            $response['conductors']=  $this->_fetch_conductors();
            $response['buses']=  $this->_fetch_buses();
            $response['routes']=  $this->_fetch_routes();
            return $response;
    }
    function _fetch_depot(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('where','config_status','1');
        $query[]=$this->rdb->qb('order_by','depot_name','asc');
        return $this->rdb->fetch('default','depots',$query);
    }
    function _fetch_depotbyid($id){
        $query[]=$this->rdb->qb('where','id_depots',$id);
        return $this->rdb->fetch('default','depots',$query,FALSE,TRUE);
    }
    function _fetch_devices(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('order_by','uid','asc');
        return $this->rdb->fetch('default','devices',$query);
    }
    function _fetch_drivers(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('order_by','driver_name','asc');
        return $this->rdb->fetch('default','drivers',$query);
    }
    function _fetch_conductors(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('order_by','conductor_name','asc');
        return $this->rdb->fetch('default','conductors',$query);
    }
    function _fetch_buses(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('order_by','bus_no','asc');
        return $this->rdb->fetch('default','buses',$query);
    }
    function _fetch_routes(){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('order_by','route_no','asc');
        return $this->rdb->fetch('default','routes',$query);
    }
    function _fetch_devicesbydepots($id=''){
        $query[]=$this->rdb->qb('where','active','1');
        $query[]=$this->rdb->qb('where','depots_id',"$id");
        $query[]=$this->rdb->qb('order_by','uid','asc');
        return $this->rdb->fetch('default','devices',$query);
    }
    
	
}