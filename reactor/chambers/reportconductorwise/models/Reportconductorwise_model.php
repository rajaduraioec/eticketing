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
 * @module      Reportconductorwise
 * @filesource  Reportconductorwise.model
 */

class Reportconductorwise_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('preports');   
    }
    function _getdata($range=array(),$depot,$conductor){
        return $this->preports->genconductorwisereport($range,$depot,$conductor);
    }
    function _getdepots(){
        $query[]=$this->rdb->qb('order_by','depot_name','asc');
        return $this->rdb->fetch('default','depots',$query);
    }
    function _getconductors(){
        $query[]=$this->rdb->qb('order_by','conductor_name','asc');
        return $this->rdb->fetch('default','conductors',$query);
    }
}