<?php

/* 
 * Reactor Framework
 * 
 * Copyright (c) 2014 - 2017, Increatech Business Solution Pvt Ltd, India
 * 
 * New BSD License
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this 
 * list of conditions and the following disclaimer. Redistributions in binary 
 * form must reproduce the above copyright notice, this list of conditions and 
 * the following disclaimer in the documentation and/or other materials provided 
 * with the distribution. Neither the name of Reactor or INCREATECH BUSINESS 
 * SOLUTION PVT LTD, nor the names of its contributors may be used to endorse 
 * or promote products derived from this software without specific prior written 
 * permission. 
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED WARRANTIES, INCLUDING, 
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
 * FOR A PARTICULAR PURPOSE ARE NONINFRINGEMENT. IN NO EVENT SHALL THE COPYRIGHT 
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package	Reactor Framework
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
 * @since	Version 1.0.0
 * @filesource
 */

defined('RAPPVERSION') or exit('No direct script access allowed');

/* 
 * Copyright 2017 Increatech Business Solution Pvt Ltd, India.
 * All Rights Reserved
 * www.increatech.com
 * info@increatech.com
 */

class Rauth
{

    public function __construct()
    {
        $this->ictcode=20489;
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    
    public function genhash(){
        return date('Y').random_string('alnum', 4).date('m').random_string('alnum', 3).date('d').random_string('alnum', 2).date('H').random_string('alnum', 3).date('i').random_string('alnum', 4).date('s');
    }
    /*
     * To be completed
     */
    public function forgot($username=''){
        $qdata[]=$this->rdb->qb('where','username',$username);
        $users=$this->rdb->fetch('default','users',$qdata);
        if(count($users)==1):
            $data['passwordhash']=$this->genhash();
            $data['last_password_change']=date('Y-m-d H:i:s');
            $condition=$this->rdb->qb('where', 'id_users',$users[0]['id_users']);
            $this->rdb->update('default','users', $data,$condition);
            $this->forgotemail($users[0]['email'], $data['passwordhash']);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    public function forgotemail($email='',$hash=''){
        $this->load->library('email'); 
   
         $this->email->from($email->from_address ,$email->from_name); 
         $this->email->to($data[0]->email);
         $this->email->subject("Password Reset "); 
         $this->email->message("Date : ".$data[0]->date."   \nTotal : ".$total." \n\n\nComapany Name : ".$company[0]->name."\nAddress : ".$company[0]->street." ".$company[0]->country_name."\nMobile No :".$company[0]->phone); 
         //Send mail 
         if($this->email->send()) 
         $message = "Email sent successfully.";
         else 
         $message = "Error in sending Email.";
    }
    /*
     * To be completed ends
     */
    
    public function isajax(){
        return $this->input->is_ajax_request();
    }
    public function has_access($modules='',$permit=''){
        if($this->is_admin()){
            return TRUE;
        }else{
            if(is_array($permit)){
                $permit_level=$permit["$modules"];
            }else{
                $qdata[]=$this->rdb->qb('select','permit_level');
                $qdata[]=$this->rdb->qb('join','ict_modules','ict_modules.id_ict_modules=ict_permission.ict_modules_id');
                $qdata[]=$this->rdb->qb('join','users','users.id_users=ict_permission.users_id');
                $qdata[]=$this->rdb->qb('where','users.username',$this->session->userdata('identity'));
                $qdata[]=$this->rdb->qb('where','ict_modules.shortname',$modules);
                $permit_level=$this->rdb->fetch('default','ict_permission',$qdata,FALSE,TRUE)->permit_level;

            }
            return ($permit_level>0);
        }
    }
    public function has_accesslevel($module='',$level='',$data=''){
        if($this->is_admin()){
            return TRUE;
        }else{
            if(is_array($data)){
                $permit_level=$data["$module"];
            }else{
                $qdata[]=$this->rdb->qb('select','permit_level');
                $qdata[]=$this->rdb->qb('join','ict_modules','ict_modules.id_ict_modules=ict_permission.ict_modules_id');
                $qdata[]=$this->rdb->qb('join','users','users.id_users=ict_permission.users_id');
                $qdata[]=$this->rdb->qb('where','users.username',$this->session->userdata('identity'));
                $qdata[]=$this->rdb->qb('where','ict_modules.shortname',$module);
                $permit_level=$this->rdb->fetch('default','ict_permission',$qdata,FALSE,TRUE)->permit_level;
            }
            $action['create'] = array('1', '3', '5', '7', '9', '11', '13', '15'); //1
            $action['read'] = array('2', '3', '6', '7', '10','11', '14', '15'); //2
            $action['update'] = array('4', '5', '6', '7', '12', '13', '14', '15'); //4
            $action['delete'] = array('8', '9', '10', '11', '12', '13', '14', '15'); //8
            $action['all'] = array('15');
            return (in_array($permit_level, $action["$level"]));
        }
    }
    public function permit_accesslevel($level='',$type=''){
        switch ($type) {
            case 'create':
                return (in_array($level, array('1', '3', '5', '7', '9', '11', '13', '15')));
                break;
            case 'read':
                return (in_array($level, array('2', '3', '6', '7', '10','11', '14', '15')));
                break;
            case 'update':
                return (in_array($level, array('4', '5', '6', '7', '12', '13', '14', '15')));
                break;
            case 'delete':
                return (in_array($level, array('8', '9', '10', '11', '12', '13', '14', '15')));
                break;
            default:
                return FALSE;
                break;
        }
    }
    function genpermission($id='',$mode='module'){
        if($mode=='module'):
            $data[]=$this->rdb->qb('where',"ict_roles_id<>'1'");
            $data[]=$this->rdb->qb('select',"id_users,ict_roles_id");
            $users=$this->rdb->fetch('default',"users",$data);
            unset($data);
            foreach($users as $user):
                if($user['ict_roles_id']==2)
                    $permit=15;
                else
                    $permit=0;
                $data[]=array(
                  'users_id' => $user['id_users'],
                  'ict_modules_id' => $id,
                  'permit_level' => $permit
                );
            endforeach;
            $this->rdb->batchcreate('default','ict_permission', $data);
            unset($data);
        elseif($mode=='user'):
            $data[]=$this->rdb->qb('select',"id_ict_modules");
            $modules=$this->rdb->fetch('default',"ict_modules",$data);
            unset($data);
            $data[]=$this->rdb->qb('where',"id_users",$id);
            $data[]=$this->rdb->qb('select',"ict_roles_id");
            $role=$this->rdb->fetch('default',"users",$data,FALSE,TRUE)->ict_roles_id;
            unset($data);
                if($role==2)
                    $permit=1;
                else
                    $permit=0;
            foreach($modules as $module):
                $data[]=array(
                  'users_id' => $id,
                  'ict_modules_id' => $module['id_ict_modules'],
                  'permit_level' => $permit
                );
            endforeach;
            $this->rdb->batchcreate('default','ict_permission', $data);
        endif;
        return;
    } 
    /**
     * Function Return whether user login or not
     * @return type Bool
     */
    public function is_login(){
        return (bool) $this->session->userdata('identity');
    }
    /**
     * Function return whether logged in user is super admin
     * @return boolean
     */
    public function is_admin(){
        return ($this->session->userdata('logincode')/$this->ictcode==1);
//        return $this->session->userdata('logincode')/$this->ictcode==1;
    }
    public function is_depot(){
        return ($this->session->userdata('usercode')==3);
    }
    public function login_depot(){
        return $this->session->userdata('depot');
    }
    public function is_client(){
        return ($this->session->userdata('usercode')>=50000);
    }  
    
    public function permitted_modules(){
        $permission=array();
        if($this->is_admin()){
            $data[]=$this->rdb->qb('select','shortname');
            $results=$this->rdb->fetch('default','ict_modules',$data);
            unset($data);
            foreach($results as $row):
                $permission[$row['shortname']]=15;
            endforeach;
        }else{
            $data[]=$this->rdb->qb('where',"permit_level>'0'");
            $data[]=$this->rdb->qb('where',"username",$this->session->userdata('identity'));
            $data[]=$this->rdb->qb('select',"shortname,permit_level");
            $data[]=$this->rdb->qb('join','ict_modules','id_ict_modules=ict_modules_id');
            $data[]=$this->rdb->qb('join','users','id_users=users_id');
            $results=$this->rdb->fetch('default','ict_permission',$data);
            unset($data);
        foreach($results as $row):
            $permission[$row['shortname']]=$row['permit_level'];
        endforeach;
        }
        return $permission;
    }
    function hashpassword($value=''){
        $r =& get_instance();
        $ekey = $r->config->item('encryption_key');
        $value=  strrev(substr($value, -2)).$ekey.strrev($value);
        $hash=hash('sha256',$value);
        return $hash;
    }   
    function checkmode($mode='',$id=''){
        $dec = explode(".", $mode);// splits up the string to any array
        $x = count($dec);
        $y = $x-2; 
        $z = $x-1; 
        $randkey = chr($dec[$y]-50);
        $checksum = chr($dec[$z]-50);
        if(($checksum-$id)!=0):
            $this->session->set_userdata('logincode', '02189');
            return TRUE;
        else:
            $i = 0;
            $init='';
            while ($i < $y)
            {
              $array[$i] = $dec[$i]+$randkey; // Works out the ascii characters actual numbers
              $init .= chr($array[$i]); //The actual decryption
              $i++;
            };
            if($init==$this->session->userdata('identity')):
                $this->session->set_userdata('logincode', '20489');
                return FALSE;
            else:
                $this->session->set_userdata('logincode', '02189');
                return FALSE;
            endif;
            $this->session->set_userdata('logincode', '04189');
            return FALSE;
        endif;
    }   
    function login($username='',$pass='',$mode=''){
        if($mode=='logme'):
	        $data[]=$this->rdb->qb('where','username',$username);
	        $data[]=$this->rdb->qb('where','password',$this->hashpassword($pass));
	        $data[]=$this->rdb->qb('where','active',1);
	        $query = $this->rdb->fetch('default','users',$data,FALSE);unset($data);  
        	if ($query->num_rows()== 1) {
	            $row = $query->row();
	            $sessdata=array(
	            	'name'=> $row->first_name.' '.$row->last_name,
	            	'identity'=> $row->username,
	            	'depot'=> $row->depots_id,
	            	'usercode'=> $row->ict_roles_id,
	            	);
	            $this->session->set_userdata($sessdata);
	            $data['last_login']=date('Y-m-d H:i:s');
	            $data['last_ip']=$this->rinput->userip();
	            $condition[]=$this->rdb->qb('where','id_users',$row->id_users);
	            $query = $this->rdb->update('default','users',$data,$condition);
	            unset($data);unset($condition);
	            return TRUE;
        	}else{
        		return FALSE;
        	}	
    	elseif($mode==''):
    		echo "Something Went Wrong.."; die();
		else:
        	return keyvalidate($username,$pass,$mode);
        endif;
    }   
    
}
