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
 * @module      Settings_model
 * @filesource  Settings_model.models
 */

class Settings_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='site_settings';
        $this->modtableid='id_'.$this->modtable;
    }
    function edit($id=''){
        $data=array(
          'sitename' => $this->rinput->post('sitename'),
          'slogan' => $this->rinput->post('slogan'),
          'email' => $this->rinput->post('email'),
          'date_format' => $this->rinput->post('date_format'),
          'footer' => $this->rinput->post('footer'),
          'inspector_pass' => $this->rinput->post('inspass')
        );
        $condition[]=$this->rdb->qb('where',$this->modtableid,1);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        return $return;
    }
    function getsitesettings()
    {
        $query[]=$this->rdb->qb('where',$this->modtableid,1);
        return $this->rdb->fetch('default',$this->modtable,$query,FALSE,TRUE);
    }
}