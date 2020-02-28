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
 * @module      Devices_model
 * @filesource  devices_model.models
 */

class Devices_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='devices';
        $this->modtableid='id_'.$this->modtable;
    }
    /*
     * New Datatable
     */
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('uid',NULL);
        $req['column_search']=array();
        $req['order']=array('id'=>'asc');
        $req['query'][]=$this->rdb->qb('join','depots','depots.id_depots=devices.depots_id');
        $req['query'][]=$this->rdb->qb('select','depots.depot_name,devices.*');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['uid'];
            $row[] = $this->getdepot($rowinfo['depots_id'])->depot_name;
            $row[] = ($rowinfo['active']==1?'Active':'Suspended');
            $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
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
        $data['uid']=$this->rinput->post('device-id');
        $data['depots_id']=$this->rinput->post('depots_id');
        $check=$this->exists($data['uid']);
        if(count($check)==0){
            $this->rdb->create('default',$this->modtable, $data);
            $return['status']=TRUE;
            $return['message']="<h4>Device Added Successfully!!!</h4>";
        }else{
            $return['status']=FALSE;
            $return['message']="<h4>Device Already Exists !!!</h4>";
        }
        return $return;
        return $return;
    }
    function edit($id=''){
        if($this->input->post('active'))
            $data['active'] = 1;
        else
            $data['active'] = 0;
        $data['depots_id']=$this->rinput->post('depots_id');
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function getdepot($id='')
    {
        $data[]=$this->rdb->qb('where','id_depots',$id);
        return $this->rdb->fetch('default','depots',$data,FALSE,TRUE);
    }
    function getdepots()
    {
        $data[]=$this->rdb->qb('order_by','depot_name','ASC');
        return $this->rdb->fetch('default','depots',$data);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','uid',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}
