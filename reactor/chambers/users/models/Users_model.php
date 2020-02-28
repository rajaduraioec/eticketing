<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      Users
 * @filesource  users.models
 */

class Users_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='users';
        $this->modtableid='id_'.$this->modtable;
    }
    
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('first_name',NULL);
        $req['column_search']=array();
        $req['order']=array('id'=>'asc');
        $req['query'][]=$this->rdb->qb('join','depots','depots.id_depots=users.depots_id');
        $req['query'][]=$this->rdb->qb('select','depots.depot_name,users.*');
        $req['query'][]=$this->rdb->qb('where',"ict_roles_id<>'1'");
        $req['query'][]=$this->rdb->qb('where',"ict_roles_id<>'2'");
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['first_name'].' '.$rowinfo['last_name'];
            $row[] = $rowinfo['depot_name'];
            $row[] = $rowinfo['username'];
            $row[] = ($rowinfo['last_login']==NULL?'Not Logged In':$rowinfo['last_login']);
            $row[] = ($rowinfo['active']==1?'Active':'Suspended');
            $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>'.
                    ' <a href="'.$this->rview->url($this->ctrl_name.'/modalpermission/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-success" title="Permission"><i class="fa fa-lock"></i></a>'.
                    ' <a href="'.$this->rview->url($this->ctrl_name.'/modalpass/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-info" title="Change Password"><i class="fa fa-key"></i></a>';
            $data[] = $row;
        }
        $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $result['totalcount'],
                    "recordsFiltered" => $result['filteredcount'],
                    "data" => $data,
                );
        return $output;
    }

    function create()
    {
        if($this->rinput->post('password')==$this->rinput->post('cpassword')):
            $data['username']=  strtolower($this->rinput->post('user-name'));
            $check=$this->exists($data['username']);
            if(count($check)==0){
                $data['first_name']=$this->rinput->post('first-name');
                $data['last_name']=$this->rinput->post('last-name');
                $data['email']=$this->rinput->post('email');
                $data['password']=$this->rauth->hashpassword($this->rinput->post('password'));
                $data['depots_id']=$this->rinput->post('depots_id');
                $data['ict_roles_id']=3;
                $data['datecreated']=date('Y-m-d H:i:s');
                $data['active']=1;
                $id=$this->rdb->create('default',$this->modtable, $data);
                $this->rauth->genpermission($id,'user');
                $return['status']=TRUE;
                $return['message']="<h4>User Successfully Created !!!</h4>"; 

            }else{
                $return['status']=FALSE;
                $return['message']="<h4>Username Already Exists !!!</h4>";
            }
        else:
            $return['status']=FALSE;
            $return['message']="<h4>Password dosen't match</h4>";
        endif;
            return $return;
    }
    function edit($id=''){
        if($this->input->post('active'))
            $data['active'] = 1;
        else
            $data['active'] = 0;
        $data['first_name']=$this->rinput->post('first-name');
        $data['last_name']=$this->rinput->post('last-name');
        $data['email']=$this->rinput->post('email');
        $data['depots_id']=$this->rinput->post('depots_id');
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        return $return;
    }
    function passwordupdate($id=''){
        if($this->rinput->post('password')==$this->rinput->post('cpassword')):
            $data['password']=$this->rauth->hashpassword($this->rinput->post('password'));
            $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
            $this->rdb->update('default',$this->modtable, $data,$condition);
            $return['status']=TRUE;
            $return['message']="<h4>Password Changed Successfully !!!</h4>";
            return $return;
        else:
            $return['status']=FALSE;
            $return['message']="<h4>Password dosen't match</h4>";
            return $return;
        endif;
    }
    function permissionupdate($id=''){
        $permitsinfo=$this->getpermission($id);
        $permissions=$this->input->post('permit'); 
        foreach($permitsinfo as $permit):
            $key=$permit['id_ict_permission'];
            $c=$r=$u=$d=0;
            if(isset($permissions[$key]['c'])){$c=1;}  
            if(isset($permissions[$key]['r'])){$r=2;}  
            if(isset($permissions[$key]['u'])){$u=4;}  
            if(isset($permissions[$key]['d'])){$d=8;}  
            $data['permit_level']=$c+$r+$u+$d;
            $condition[]=$this->rdb->qb('where','id_ict_permission',$key);
            $this->rdb->update('default','ict_permission', $data,$condition);
            unset($condition);
            unset($data);
        endforeach;
        $return['status']=TRUE;
        $return['message']="Permission Updated Sucessfully";
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function getdepots()
    {
        $data[]=$this->rdb->qb('order_by','depot_name','ASC');
        return $this->rdb->fetch('default','depots',$data);
    }
    function getpermission($id='')
    {
        $data[]=$this->rdb->qb('order_by','ict_modules.name','ASC');
        $data[]=$this->rdb->qb('select','ict_modules.name,permit_level,id_ict_permission');
        $data[]=$this->rdb->qb('where','users_id',$id);
        $data[]=$this->rdb->qb('join','ict_modules','ict_modules.id_ict_modules=ict_permission.ict_modules_id');
        return $this->rdb->fetch('default','ict_permission',$data);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','username',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}