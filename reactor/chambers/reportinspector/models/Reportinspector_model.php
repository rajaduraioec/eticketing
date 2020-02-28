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
 * @filesource  Reportinspector.model
 */

class Reportinspector_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('preports');   
    }
    function _getdata($range=array(),$depot,$inspector,$bus,$route){
        return $this->preports->geninspectionreport($range,$depot,$inspector,$bus,$route);
    }
    function _getdepots(){
        $query[]=$this->rdb->qb('order_by','depot_name','asc');
        return $this->rdb->fetch('default','depots',$query);
    }
    function _getinspectors(){
        $query[]=$this->rdb->qb('order_by','inspector_name','asc');
        return $this->rdb->fetch('default','inspectors',$query);
    }
    function _getbuses(){
        $query[]=$this->rdb->qb('order_by','bus_no','asc');
        return $this->rdb->fetch('default','buses',$query);
    }
    function _getroutes(){
        $query[]=$this->rdb->qb('order_by','route_no','asc');
        return $this->rdb->fetch('default','routes',$query);
    }

}