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
 * @module      Collectionanalysis
 * @filesource  Collectionanalysis.model
 */

class Collectionanalysis_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ranalytics');   
    }
    function _getdata($range=array(),$depot){
        return $this->ranalytics->gencollectionanalysis($range,$depot);
    }
    function _getdepots(){
        $query[]=$this->rdb->qb('order_by','depot_name','asc');
        return $this->rdb->fetch('default','depots',$query);
    }

}