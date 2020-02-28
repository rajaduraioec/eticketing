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
 * @module      Depots
 * @filesource  depots.model
 */

class Depots_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='depots';
        $this->modtableid='id_'.$this->modtable;
        $this->module_name="depots";
    }
    /*
     * New Datatable
     */
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('depot_name','active',NULL);
        $req['column_search']=array('depot_name');
        $req['order']=array('id'=>'asc');
        $isedit=$this->rauth->has_accesslevel($this->module_name,'update');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['depot_name'];
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
        $data['depot_name'] = $this->rinput->post('depot-name');
        $check=$this->exists($data['depot_name']);
        if(count($check)==0){
            $this->rdb->create('default',$this->modtable, $data);
            $return['status']=TRUE;
            $return['message']="<h4>Depot Successfully Created !!!</h4>";
        }else{
            $return['status']=FALSE;
            $return['message']="<h4>Depot Already Exists !!!</h4>";
        }
        return $return;
    }
    function edit($id=''){
        $data['depot_name'] = $this->rinput->post('depot-name');
        if($this->rinput->post('active')):
            $data['active'] = 1;
            else:
            $data['active'] = 0;
        endif;
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $check=$this->exists($data['depot_name']);
        if(count($check)==0){
            $this->rdb->update('default',$this->modtable, $data,$condition);
            $return['status']=TRUE;
            $return['message']="Updated Sucessfully";
        }elseif($check[0][$this->modtableid]==$id){
            $this->rdb->update('default',$this->modtable, $data,$condition);
            $return['status']=TRUE;
            $return['message']="Updated Sucessfully";
        }else{
            $return['status']=FALSE;
            $return['message']="Depot Already exists";
            
        }
            unset($data);
            unset($condition);
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','depot_name',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}