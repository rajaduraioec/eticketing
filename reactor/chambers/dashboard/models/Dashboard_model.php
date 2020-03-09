<?php

if (!defined('RAPPVERSION'))
    exit('No direct script access allowed');
/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Dashboard
 * @filesource  Dashboard.model
 */

class Dashboard_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('dashlib');   
    }
    function _getlivedata(){
        if($this->rauth->is_depot())
            return $this->dashlib->genlivestatusbydepot($this->rauth->login_depot());
        else
            return $this->dashlib->genlivestatus();
            // return 1;
    }
    function _getlastdata(){
        if($this->rauth->is_depot())
            return $this->dashlib->genlaststatusbydepot($this->rauth->login_depot());
        else
            return $this->dashlib->genlaststatus();
            // return 1;
    }
    function _getdepotdata($id){
        $return['live']= $this->dashlib->genlivestatusbydepot($id);
        $return['last']= $this->dashlib->genlaststatusbydepot($id);
        $query[]=$this->rdb->qb('where','id_depots',$id);
        $return['depot']= $this->rdb->fetch('default','depots',$query,FALSE,TRUE);
        unset($query);
        return $return;
    }

}