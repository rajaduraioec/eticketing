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
 * @module      Deviceadmins
 * @filesource  Deviceadmins.models
 */
 
class Deviceadmins_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='deviceadmins';
        $this->modtableid='id_'.$this->modtable;
    }
    /*
     * New Datatable
     */
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('depot_name','admin_name','admin_id','active',NULL);
        $req['column_search']=array('depot_name');
        $req['order']=array('id'=>'asc');
        $isedit=$this->rauth->has_accesslevel($this->module_name,'update');
        $req['query'][]=$this->rdb->qb('join','depots','depots.id_depots=deviceadmins.depots_id');
        $req['query'][]=$this->rdb->qb('select','depots.depot_name,deviceadmins.*');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['depot_name'];
            $row[] = $rowinfo['admin_name'];
            $row[] = $rowinfo['admin_id'];
            $row[] = ($rowinfo['active']=='1'?'Active':'Not Active');
             if($isedit)
            $row[] = '<a href="'.$this->rview->url($this->ctrl_name).'/edit/'.$rowinfo[$this->modtableid].'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
             else
            $row[]='';
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
        $data['depots_id'] = $this->rinput->post('depot-name');
        $data['admin_name'] = $this->rinput->post('admin-name');
        $data['admin_id'] = $this->rinput->post('admin-id');
        $data['admin_pass'] = $this->rinput->post('admin-pass');
        $data['modifiedon'] = date('Y-m-d H:i:s');
        $check=$this->exists($data['admin_id']);
        if(count($check)==0)
        {
        	$this->rdb->create('default',$this->modtable, $data);
	        $return['status']=TRUE;
	        $return['message']="<h4>Admin User Successfully Added !!!</h4>";
	        $query[]=$this->rdb->qb('where','depots_id',$data['depots_id']);
	        if(count($this->rdb->fetch('default','headerfooters',$query))==1){
	        	unset($query);
	        	$ddata['config_status'] = 1;
	    		$condition[]=$this->rdb->qb('where','id_depots',$data['depots_id']);
	        	$this->rdb->update('default','depots', $ddata,$condition);
	        }
        }else{
        	$return['status']=FALSE;
	        $return['message']="<h4>Admin User Already Exist !!!</h4>";
        }
        return $return;
    }
    
    function get_depots()
	{
		$data[]=$this->rdb->qb('join',$this->modtable,"depots.id_depots=$this->modtable.depots_id",'left');
		$data[]=$this->rdb->qb('where','admin_id',NULL);
        return $this->rdb->fetch('default','depots',$data);
	}
	
    function edit($id=''){
    	$data['modifiedon'] = date('Y-m-d H:i:s');
        if($this->rinput->post('active')):
            $data['active'] = 1;
            else:
            $data['active'] = 0;
        endif;
        $data['admin_name'] = $this->rinput->post('admin-name');
        $data['admin_id'] = $this->rinput->post('admin-id');
        $data['admin_pass'] = $this->rinput->post('admin-pass');
    	$check=$this->exists($data['admin_id']);
    	if(count($check)==0){
    		$condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        	$this->rdb->update('default',$this->modtable, $data,$condition);
        	$return['status']=TRUE;
        	$return['message']="Updated Sucessfully";
    	}elseif($check[0]['id_deviceadmins']==$id){
    		$condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        	$this->rdb->update('default',$this->modtable, $data,$condition);
        	$return['status']=TRUE;
        	$return['message']="Updated Sucessfully";
    	}else{
    		$return['status']=FALSE;
        	$return['message']="Already Name Exist !!!";
    	}
        unset($data);   unset($condition);
        return $return;
    }
    function getinfo($id='')
    {
		$data[]=$this->rdb->qb('join','depots',"$this->modtable.depots_id=depots.id_depots");
		$data[]=$this->rdb->qb('select','depots.depot_name,deviceadmins.*');
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','admin_id',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}