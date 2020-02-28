<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Auth
 * @filesource  auth.model
 */

class Auth_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    function edit($id=''){
        $data=array(
          'first_name' => $this->rinput->post('first_name'),
          'last_name' => $this->rinput->post('last_name'),
          'email' => $this->rinput->post('email')
        );
        $condition[]=$this->rdb->qb('where','id_users',$id);
        $this->rdb->update('default','users', $data,$condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        return $return;
    }
    function passwordupdate(){
        
        if($this->rinput->post('password')==$this->rinput->post('cpassword')):
            $query[]=$this->rdb->qb('where','username',$this->session->userdata('identity'));
            $profile=$this->rdb->fetch('default','users',$query,FALSE,TRUE);
            unset($query);
            if($profile->password==$this->rauth->hashpassword($this->rinput->post('opassword'))):
                $data['password']=$this->rauth->hashpassword($this->rinput->post('password'));
                $condition[]=$this->rdb->qb('where','id_users',$profile->id_users);
                $this->rdb->update('default','users', $data,$condition);
                $return['status']=TRUE;
                $return['message']="<h4>Password Changed Successfully !!!</h4>";
                return $return;
            else:
                $return['status']=FALSE;
                $return['message']="<h4>Old Password dosen't match</h4>";
                return $return;
            endif;
        else:
            $return['status']=FALSE;
            $return['message']="<h4>Password dosen't match</h4>";
            return $return;
        endif;
    }
    function getprofile()
    {
        $query[]=$this->rdb->qb('where','username',$this->session->userdata('identity'));
        return $this->rdb->fetch('default','users',$query,FALSE,TRUE);
    }
}