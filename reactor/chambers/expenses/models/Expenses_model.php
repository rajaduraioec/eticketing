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
 * @module      Expenses_model
 * @filesource  Expenses_model.models
 */
 

class Expenses_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='expenses';
        $this->modtableid='id_'.$this->modtable;
    }
    
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('expense_name','active',NULL);
        $req['column_search']=array('expense_name');
        $req['order']=array('id'=>'asc');
        $isedit=$this->rauth->has_accesslevel($this->module_name,'update');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['expense_name'];
            $row[] = ($rowinfo['active']=='1'?'Active':'Not Active');
            if($isedit):
                if($rowinfo[$this->modtableid]=='1')
                    $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit" disabled><i class="fa fa-edit"></i></a>';
                else
                    $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
            else:
                $row[]='';
            endif;
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
        $data['expense_name'] = $this->rinput->post('expense-name');
        $data['modifiedon'] = date('Y-m-d H:i:s');
        $check=$this->exists($data['expense_name']);
        if(count($check)==0){
            $this->rdb->create('default',$this->modtable, $data);
            $return['status']=TRUE;
            $return['message']="<h4>Expenses Successfully Created !!!</h4>";
        }else{
            $return['status']=FALSE;
            $return['message']="<h4>Expenses Already Exists !!!</h4>";
        }
        return $return;
    }
    function edit($id=''){
        if($this->rinput->post('active')):
            $data['active'] = 1;
            else:
            $data['active'] = 0;
        endif;
        $data['modifiedon'] = date('Y-m-d H:i:s');
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        unset($data);  unset($condition);
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','expense_name',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
}