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
 * @module      Conductors
 * @filesource  Conductors.models
 */
 
class Conductors_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='conductors';
        $this->modtableid='id_'.$this->modtable;
    }
    /*
     * New Datatable
     */
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('conductor_name','conductor_id','active',NULL);
        $req['column_search']=array('conductor_name');
        $req['order']=array('id'=>'asc');
        $isedit=$this->rauth->has_accesslevel($this->module_name,'update');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['conductor_name'];
            $row[] = $rowinfo['conductor_id'];
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
        $data['conductor_name'] = $this->rinput->post('conductor-name');
        $data['conductor_id'] = $this->rinput->post('conductor-id');
        $data['conductor_pass'] = $this->rinput->post('conductor-pass');
		$data['modifiedon'] = date('Y-m-d H:i:s');        
        $check=$this->exists($data['conductor_id']);
        if(count($check)==0)
        {
        	$this->rdb->create('default',$this->modtable, $data);
	        $return['status']=TRUE;
	        $return['message']="<h4>Conductor Details Successfully Added !!!</h4>";
        }else{
        	$return['status']=FLASE;
	        $return['message']="<h4>Conductor Details Already Exist !!!</h4>";
        }
        return $return;
    }
    function edit($id=''){
    	$data['conductor_name'] = $this->rinput->post('conductor-name');
        $data['conductor_id'] = $this->rinput->post('conductor-id');
        $data['conductor_pass'] = $this->rinput->post('conductor-pass');
        if($this->rinput->post('active')):
            $data['active'] = 1;
            else:
            $data['active'] = 0;
        endif;
        $data['modifiedon'] = date('Y-m-d H:i:s');
    	$check=$this->exists($data['conductor_id']);
    	if(count($check)==0){
    		$condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        	$this->rdb->update('default',$this->modtable, $data,$condition);
        	$return['status']=TRUE;
        	$return['message']="Updated Sucessfully";
    	}elseif($check[0]['id_conductors']==$id){
    		$condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        	$this->rdb->update('default',$this->modtable, $data,$condition);
        	$return['status']=TRUE;
        	$return['message']="Updated Sucessfully";
    	}else{
    		$return['status']=FALSE;
        	$return['message']="Already Conductor Name Exist !!!";
    	}
        
        unset($data);   unset($condition);
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','conductor_id',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}